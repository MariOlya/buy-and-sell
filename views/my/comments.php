<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var Ads[] $adsWithComments */

use omarinina\domain\models\ads\Ads;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<section class="comments">
    <div class="comments__wrapper">
        <?php if (!$adsWithComments) : ?>
            <p class="comments__message">У ваших публикаций еще нет комментариев.</p>
        <?php else : ?>
        <h1 class="visually-hidden">Страница комментариев</h1>
            <?php foreach ($adsWithComments as $ad) : ?>
                <div class="comments__block">
                    <div class="comments__header">
                        <a href="<?= Url::to(['offers/view', 'id' => $ad->id]) ?>" class="announce-card">
                            <h2 class="announce-card__title"><?= $ad->name ?></h2>
                            <span class="announce-card__info">
                      <span class="announce-card__price">₽ <?= $ad->price ?></span>
                      <span class="announce-card__type"><?= $ad->type->name ?></span>
                    </span>
                        </a>
                    </div>
                    <ul class="comments-list">
                        <?php foreach ($ad->comments as $comment) : ?>
                            <li class="js-card">
                                <div class="comment-card">
                                    <div class="comment-card__header">
                                        <a href="#" class="comment-card__avatar avatar">
                                            <img
                                                    src="<?= $comment->authorUser->avatarSrc ?>"
                                                    srcset="<?= $comment->authorUser->avatarSrc ?> 2x"
                                                    alt="Аватар пользователя">
                                        </a>
                                        <p class="comment-card__author">
                                            <?= $comment->authorUser->name . ' ' . $comment->authorUser->lastName ?>
                                        </p>
                                    </div>
                                    <div class="comment-card__content">
                                        <p><?= $comment->text ?></p>
                                    </div>
                                    <?php
                                    echo Html::a(
                                        'Удалить',
                                        Url::to([
                                                    'my/delete-comment',
                                                'id' => $ad->id,
                                                'commentId' => $comment->id
                                            ]),
                                        ['class'=>'comment-card__delete js-delete']
                                    );
                                    ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
