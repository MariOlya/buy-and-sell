<?php

declare(strict_types=1);

namespace omarinina\application\services\ad\interfaces;

use omarinina\domain\models\Users;
use yii\db\ActiveQuery;

interface FilterAdsGetInterface
{
    public function getNewAds(): array;
    public function getPopularAds(): array;
    public function getSearchedAds(string $search): ?array;
    public function getCategoryAds(int $categoryId): ActiveQuery;
    public function getUserAdsWithComments(Users $user);
}