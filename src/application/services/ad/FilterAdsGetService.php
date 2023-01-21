<?php

namespace omarinina\application\services\ad;

use omarinina\application\services\ad\interfaces\FilterAdsGetInterface;
use omarinina\domain\models\ads\Ads;
use omarinina\domain\models\Users;
use yii\db\ActiveQuery;
use yii\sphinx\Query;

class FilterAdsGetService implements FilterAdsGetInterface
{

    public function getNewAds(): array
    {
        return Ads::find()->orderBy('createAt DESC')->limit(8)->all();
    }

    public function getPopularAds(): array
    {
        return Ads::find()
            ->joinWith(['comments'])
            ->select(['ads.*', 'COUNT(comments.id) AS commentsCount'])
            ->groupBy(['ads.id'])
            ->orderBy(['commentsCount' => SORT_DESC])
            ->limit(8)
            ->all();
    }

    public function getSearchedAds(string $search): ?array
    {
        $query = new Query();
        return $query->from('idx_ads')
            ->match($search)
            ->all();
    }

    /**
     * @param int $categoryId
     * @return ActiveQuery
     */
    public function getCategoryAds(int $categoryId): ActiveQuery
    {
        return Ads::find()
            ->joinWith('adsToCategories')
            ->where(['categoryId' => $categoryId])
            ->orderBy('createAt DESC');
    }

    /**
     * @param Users $user
     * @return Ads[]
     */
    public function getUserAdsWithComments(Users $user): array
    {
        return $user
            ->getAds()
            ->joinWith(['comments' => function (ActiveQuery $query) {
                $query->with(['authorUser']);
            }])
            ->groupBy(['ads.id'])
            ->having('MAX(comments.id) > 0')
            ->orderBy(['MAX(comments.createAt)' => SORT_DESC])
            ->all();
    }
}
