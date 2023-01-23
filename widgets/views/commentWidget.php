<?php

declare(strict_types=1);

/** @var Comments $comment */

use omarinina\domain\models\ads\Comments;

?>

<li>
    <div class="comment-card">
        <div class="comment-card__header">
            <a href="#" class="comment-card__avatar avatar">
                <img
                        src="<?= $comment->authorUser->avatarSrc ?>"
                        srcset="<?= $comment->authorUser->avatarSrc ?>"
                        alt="Аватар пользователя"
                >
            </a>
            <p class="comment-card__author"><?= $comment->authorUser->name . ' ' . $comment->authorUser->lastName ?></p>
        </div>
        <div class="comment-card__content">
            <p><?= $comment->text ?></p>
        </div>
    </div>
</li>
