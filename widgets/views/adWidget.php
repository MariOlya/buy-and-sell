<?php

declare(strict_types=1);

/** @var Ads $ad */

use omarinina\domain\models\ads\AdCategories;
use omarinina\domain\models\ads\Images;
use omarinina\domain\models\ads\Ads;
use yii\helpers\Html;
use yii\helpers\Url;

if (is_array($ad)) {
    /** @var AdCategories[] $categories */
    $categories = AdCategories::find()->where(['id' => $ad['categories']])->all();
    if ($ad['images']) {
        $images = explode(',', $ad['images']);
        $imageId = (int)array_pop($images);
        /** @var Images $image */
        $image = Images::findOne($imageId);
    }
}

?>

<?php if (is_array($ad)) : ?>
<li class="tickets-list__item">
    <div class="ticket-card ticket-card--color01">
        <div class="ticket-card__img">
            <img
                    src="<?= $image->imageSrc ?? '/img/blank.png' ?>"
                    srcset="<?= $image->imageSrc ?? '/img/blank@2x.png 2x' ?>" alt="Изображение товара">
        </div>
        <div class="ticket-card__info">
            <span class="ticket-card__label"><?= $ad['type'] ?></span>
            <?php if ($categories) : ?>
            <div class="ticket-card__categories">
                <?php foreach ($categories as $category) : ?>
                <a href="<?= Url::to(['offers/category', 'categoryId' => $category->id]) ?>"><?= $category->name ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="ticket-card__header">
                <h3 class="ticket-card__title">
                    <a href="<?= Url::to(['offers/view', 'id' => $ad['id']]) ?>"><?= $ad['name'] ?></a>
                </h3>
                <p class="ticket-card__price"><span class="js-sum"><?= $ad['price'] ?></span> ₽</p>
            </div>
            <div class="ticket-card__desc">
                <p><?= mb_strimwidth($ad['description'], 0, 55, '...') ?></p>
            </div>
        </div>
    </div>
</li>
<?php else : ?>
<li class="tickets-list__item">
    <div class="ticket-card ticket-card--color01">
        <div class="ticket-card__img">
            <img
                    src="<?= $ad->getFirstImage() ?? '/img/blank.png' ?>"
                    srcset="<?= $ad->getFirstImage() ?? '/img/blank@2x.png 2x' ?>" alt="Изображение товара">
        </div>
        <div class="ticket-card__info">
            <span class="ticket-card__label"><?= $ad->type->name ?></span>
            <div class="ticket-card__categories">
                <?php foreach ($ad->adCategories as $category) : ?>
                    <a href="<?= Url::to(['offers/category', 'categoryId' => $category->id]) ?>">
                        <?= $category->name ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="ticket-card__header">
                <h3 class="ticket-card__title">
                    <a
                        <?php if (Yii::$app->request->url === '/my/index') : ?>
                            href="<?= Url::to(['offers/edit', 'id' => $ad->id]) ?>"
                        <?php else : ?>
                            href="<?= Url::to(['offers/view', 'id' => $ad->id]) ?>"
                        <?php endif; ?>
                    ><?= $ad->name ?></a>
                </h3>
                <p class="ticket-card__price"><span class="js-sum"><?= $ad->price ?></span> ₽</p>
            </div>
            <?php if (Yii::$app->request->url !== '/my/index') : ?>
            <div class="ticket-card__desc">
                <p><?= mb_strimwidth($ad->description, 0, 55, '...') ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php if (Yii::$app->request->url === '/my/index') : ?>
            <?php
            echo Html::a('Удалить', Url::to(['my/delete', 'id' => $ad->id]), ['class'=>'ticket-card__del js-delete']);
            ?>
        <?php endif; ?>
    </div>
</li>
<?php endif; ?>
