<?php

declare(strict_types=1);

use omarinina\domain\models\ads\AdCategories;
use yii\base\InvalidConfigException;
use yii\widgets\Menu;

/** @var AdCategories[] $categories */

/**
 * @param AdCategories[] $categories
 * @return array
 * @throws InvalidConfigException
 */
function getCategoryItems(array $categories):array
{
    $items = [];
    foreach ($categories as $category) {
        $items[] = [
            'label' => '<span class="category-tile__image">
            <img 
                src="' . Yii::$app->params['categorySrc'][array_rand(Yii::$app->params['categorySrc'])] . '" 
                srcset="' . Yii::$app->params['categorySrc'][array_rand(Yii::$app->params['categorySrc'])] . ' 2x"
                alt="Иконка категории">
            </span>
            <span class="category-tile__label">' .
                $category->name . ' <span class="category-tile__qty js-qty">' .
                $category->getAmountAds() .'</span></span>',
            'url' => ['offers/category', 'categoryId' => $category->id],
        ];
    }
    return $items;
}

$defaultCss = Yii::$app->request->url === '/' ?
    'categories-list__item category-tile--default' :
    'categories-list__item';


    echo Menu::widget([
        'encodeLabels' => false,
        'items' =>getCategoryItems($categories),
        'activeCssClass' => 'categories-list__item category-tile--active',
        'linkTemplate' => '<a href="{url}">{label}</a>',
        'options' => [
            'class' => 'categories-list__wrapper'
        ],
        'itemOptions' => [
            'class' => $defaultCss],
    ]);
