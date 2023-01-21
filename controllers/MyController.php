<?php

declare(strict_types=1);

namespace app\controllers;

use omarinina\application\services\ad\interfaces\FilterAdsGetInterface;
use omarinina\domain\models\ads\Ads;
use omarinina\domain\models\ads\Comments;
use omarinina\domain\models\Users;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/** This controller works with personal ads so all actions without additional comments for ads */
class MyController extends SecurityController
{
    /** @var FilterAdsGetInterface */
    private FilterAdsGetInterface $filterAds;

    public function __construct(
        $id,
        $module,
        FilterAdsGetInterface $filterAds,
        $config = []
    ) {
        $this->filterAds = $filterAds;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $rules = parent::behaviors();
        $rule = [
            'allow' => true,
            'actions' => ['delete', 'delete-comment'],
            'matchCallback' => function () {
                $currentUser = Yii::$app->user->id;
                $adUser = $this->findModel(Yii::$app->request->get('id'))->author;
                return $currentUser === $adUser;
            }
        ];
        array_unshift($rules['access']['rules'], $rule);
        return $rules;
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
            ->with('images', 'adsToCategories', 'adsToImages', 'comments')
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.', 404);
        }

        return $model;
    }


    public function actionIndex()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;
        $userAds = $user->getAds()->orderBy('createAt DESC')->all();

        return $this->render('index', [
           'userAds' => $userAds
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        $deletedAd = $this->findModel($id);

        $deletedAd->deleteAd();

        return $this->redirect('index');
    }

    /**
     * @return string
     */
    public function actionComments(): string
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;
        $adsWithComments = $this->filterAds->getUserAdsWithComments($user);

        return $this->render('comments', [
            'adsWithComments' => $adsWithComments
        ]);
    }

    /**
     * @param int $id
     * @param int $commentId
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteComment(int $id, int $commentId): Response
    {
        $comment = Comments::findOne($commentId);

        if (!$comment) {
            throw new NotFoundHttpException('Comment is not found', 404);
        }

        $comment->deleteComment();

        return $this->redirect('comments');
    }
}
