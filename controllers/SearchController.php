<?php

namespace app\controllers;

use omarinina\application\services\ad\interfaces\FilterAdsGetInterface;
use omarinina\infrastructure\models\forms\SearchForm;
use Yii;
use yii\web\Controller;

class SearchController extends Controller
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

    public function actionIndex()
    {
        $searchForm = new SearchForm();

        if (Yii::$app->request->getIsGet()) {
            $search = Yii::$app->request->get();
            $searchForm->load($search);
            $value = $searchForm->search;

            if ($searchForm->validate()) {
                $searchedAds = $this->filterAds->getSearchedAds($value);
                $newAds = $this->filterAds->getNewAds();

                return $this->render('index', [
                    'value' => $value ?? null,
                    'searchedAds' => $searchedAds ?? null,
                    'newAds' => $newAds
                ]);
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}