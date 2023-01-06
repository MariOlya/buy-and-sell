<?php

declare(strict_types=1);

namespace app\controllers;

use Kreait\Firebase\Contract\Database;
use omarinina\application\services\realtimeDatabase\RealtimeDatabaseInitializeService;
use omarinina\application\factories\ad\dto\NewAdDto;
use omarinina\application\factories\ad\dto\NewCommentDto;
use omarinina\application\factories\ad\interfaces\AdFactoryInterface;
use omarinina\application\factories\ad\interfaces\CommentFactoryInterface;
use omarinina\application\services\ad\interfaces\FilterAdsGetInterface;
use omarinina\domain\models\ads\AdCategories;
use omarinina\domain\models\ads\Ads;
use omarinina\domain\models\ads\AdTypes;
use omarinina\infrastructure\models\forms\AdCreateForm;
use omarinina\infrastructure\models\forms\AdEditForm;
use omarinina\infrastructure\models\forms\CommentCreateForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\di\NotInstantiableException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class OffersController extends Controller
{
    /** @var AdFactoryInterface */
    private AdFactoryInterface $adFactory;

    /** @var CommentFactoryInterface  */
    private CommentFactoryInterface $commentFactory;

    /** @var FilterAdsGetInterface */
    private FilterAdsGetInterface $filterAds;

    /** @var Database */
    private Database $database;

    public function __construct(
        $id,
        $module,
        AdFactoryInterface $adFactory,
        CommentFactoryInterface $commentFactory,
        FilterAdsGetInterface $filterAds,
        $config = []
    ) {
        $this->adFactory = $adFactory;
        $this->commentFactory = $commentFactory;
        $this->filterAds = $filterAds;
        $this->database = RealtimeDatabaseInitializeService::intializeRealtimeDatabase();
        parent::__construct($id, $module, $config);
    }

    public function init(): void
    {
        parent::init();
        Yii::$app->user->loginUrl = ['login/index'];
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['add', 'edit', 'chat'],
                'rules' => [
                    [
                        'actions' => ['add', 'chat'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['edit'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $currentUser = Yii::$app->user->id;
                            $adUser = $this->findModel(Yii::$app->request->get('id'))->author;
                            return $currentUser === $adUser;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return Ads
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Ads
    {
        /** @var Ads $model */
        $model = Ads::find()
            ->where(['id' => $id])
            ->with('images', 'adsToCategories', 'adsToImages')
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.', 404);
        }

        return $model;
    }

    /**
     * @return string|Response
     */
    public function actionAdd(): Response|string
    {
        $categories = AdCategories::find()->all();
        $types = AdTypes::find()->all();
        $adCreateForm = new AdCreateForm();
        $author = Yii::$app->user->id;

        if (Yii::$app->request->getIsPost()) {
            $adCreateForm->load(Yii::$app->request->post());
            $adCreateForm->images = UploadedFile::getInstances($adCreateForm, 'images');

            if ($adCreateForm->validate()) {
                $newAd = $this->adFactory->createNewAd(new NewAdDto($adCreateForm, $author));

                return $this->redirect(['view', 'id' => $newAd->id]);
            }
        }

        return $this->render('add', [
            'model' => $adCreateForm,
            'categories' => $categories,
            'types' => $types,
            'currentAd' => null
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string|Response
    {
        $currentAd = Ads::findOne($id);

        if (!$currentAd) {
            throw new NotFoundHttpException('Ad is not found', 404);
        }

        $commentForm = new CommentCreateForm();
        $currentUser = Yii::$app->user->id;

        if (Yii::$app->request->getIsPost()) {
            $commentForm->load(Yii::$app->request->post());
            if ($commentForm->validate()) {
                $this->commentFactory->createNewComment(new NewCommentDto(
                    $commentForm,
                    $currentUser,
                    $id
                ));
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('view', [
            'currentAd' => $currentAd,
            'model' => $commentForm
        ]);
    }

    /**
     * @param int $categoryId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory(int $categoryId)
    {
        $currentCategory = AdCategories::findOne($categoryId);

        if (!$currentCategory) {
            throw new NotFoundHttpException('Category is not found', 404);
        }

        $categories = AdCategories::find()->joinWith(['adsToCategories'])->where(['not', ['adId' => null]])->all();
        $categoryAds = $this->filterAds->getCategoryAds($categoryId);

        $pagination = new Pagination([
            'totalCount' => $categoryAds->count(),
            'pageSize' => Yii::$app->params['categoryAdsOnPage'],
            'forcePageParam' => false,
            'pageSizeParam' => false
        ]);

        $categoryAdsWithPagination = $categoryAds->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();


        return $this->render('category', [
            'categories' => $categories,
            'categoryAds' => $categoryAdsWithPagination,
            'currentCategory' => $currentCategory,
            'pagination' => $pagination
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws NotInstantiableException
     * @throws ServerErrorHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionEdit(int $id): Response|string
    {
        $currentAd = $this->findModel($id);

        $currentUser = Yii::$app->user->id;
        $adUser = $currentAd->author;

        if ($currentUser !== $adUser) {
            throw new NotFoundHttpException('You cannot change this task', 403);
        }

        $categories = AdCategories::find()->all();
        $types = AdTypes::find()->all();
        $adEditForm = new AdEditForm();

        if (Yii::$app->request->getIsPost()) {
            $adEditForm->load(Yii::$app->request->post());
            if ($adEditForm->images) {
                $adEditForm->images = UploadedFile::getInstances($adEditForm, 'images');
            }

            if ($adEditForm->validate()) {
                $currentAd->updateAd($adEditForm);

                return $this->redirect(['view', 'id' => $currentAd->id]);
            }
        }

        return $this->render('add', [
            'model' => $adEditForm,
            'categories' => $categories,
            'types' => $types,
            'currentAd' => $currentAd
        ]);
    }

    public function actionAjaxChat()
    {
        
    }
}
