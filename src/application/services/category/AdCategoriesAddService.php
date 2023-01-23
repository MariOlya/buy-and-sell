<?php

declare(strict_types=1);

namespace omarinina\application\services\category;

use omarinina\application\services\category\interfaces\AdCategoriesAddInterface;
use omarinina\domain\models\ads\AdsToCategories;

class AdCategoriesAddService implements AdCategoriesAddInterface
{
    /**
     * @param array $categories
     * @param int $adId
     * @return void
     */
    public function addAdCategories(array $categories, int $adId): void
    {
        foreach ($categories as $category) {
            $newAdCategory = new AdsToCategories();
            $newAdCategory->categoryId = $category;
            $newAdCategory->adId = $adId;
            $newAdCategory->save(false);
        }
    }
}