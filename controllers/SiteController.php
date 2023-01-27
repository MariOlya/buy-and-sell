<?php

namespace app\controllers;

use omarinina\application\services\ad\interfaces\FilterAdsGetInterface;
use omarinina\domain\models\ads\AdCategories;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\ErrorAction;

class SiteController extends Controller
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

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $this->layout = 'error';

        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'main';

        $newAds = $this->filterAds->getNewAds();
        $popularAds = $this->filterAds->getPopularAds();
        $categories = AdCategories::find()->joinWith(['adsToCategories'])->where(['not', ['adId' => null]])->all();

        return $this->render('index', [
            'newAds' => $newAds,
            'popularAds' => $popularAds,
            'categories' => $categories
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
