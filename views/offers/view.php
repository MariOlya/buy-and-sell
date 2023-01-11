<?php

declare(strict_types=1);

use app\widgets\CommentWidget;
use omarinina\domain\models\ads\Ads;
use omarinina\domain\models\Users;
use omarinina\infrastructure\models\forms\CommentCreateForm;
use omarinina\infrastructure\constants\AdConstants;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Ads $currentAd */
/** @var CommentCreateForm $model */
/** @var array $authorChats */

/** @var Users $currentUser */
$currentUser = Yii::$app->user->identity;
$isBuyer = (Yii::$app->user->id !== $currentAd->author && $currentAd->type->name !== AdConstants::TYPE_BUY) || (Yii::$app->user->id === $currentAd->author && $currentAd->type->name !== AdConstants::TYPE_SELL);
if ($currentUser) {
    $currentRefForBuyer = "ads/{$currentAd->id}/rooms/{$currentUser->id}/messages";
}
?>

<section class="ticket">
    <div class="ticket__wrapper">
        <h1 class="visually-hidden">Карточка объявления</h1>
        <div class="ticket__content">
            <div class="ticket__img">
                <img
                        src="<?= $currentAd->images[0]->imageSrc ?? Yii::$app->params['defaultImageSrc'] ?>"
                        srcset="<?= $currentAd->images[0]->imageSrc ?? Yii::$app->params['defaultImageSrc'] ?>"
                        alt="Изображение товара"
                >
            </div>
            <div class="ticket__info">
                <h2 class="ticket__title"><?= $currentAd->name ?></h2>
                <div class="ticket__header">
                    <p class="ticket__price"><span class="js-sum"><?= $currentAd->price ?></span> ₽</p>
                    <p class="ticket__action"><?= strtoupper($currentAd->type->name) ?></p>
                </div>
                <div class="ticket__desc">
                    <p><?= $currentAd->description ?></p>
                </div>
                <div class="ticket__data">
                    <p>
                        <b>Дата добавления:</b>
                        <span><?= Yii::$app->formatter->asDate($currentAd->createAt, 'dd MMMM yyyy') ?></span>
                    </p>
                    <p>
                        <b>Автор:</b>
                        <a href="#"><?= $currentAd->authorUser->name . ' ' . $currentAd->authorUser->lastName ?></a>
                    </p>
                    <p>
                        <b>Контакты:</b>
                        <a href="mailto:<?= $currentAd->email ?>"><?= $currentAd->email ?></a>
                    </p>
                </div>
                <ul class="ticket__tags">
                    <?php foreach ($currentAd->adCategories as $category) : ?>
                        <?php
                        $categorySrc = Yii::$app->params['categorySrc'][array_rand(Yii::$app->params['categorySrc'])]
                        ?>
                    <li>
                        <a href="#" class="category-tile category-tile--small">
                            <span class="category-tile__image">
                              <img src="<?= $categorySrc ?>" srcset="<?= $categorySrc ?> 2x" alt="Иконка категории">
                            </span>
                            <span class="category-tile__label"><?= $category->name ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="ticket__comments">
            <?php if (Yii::$app->user->isGuest) : ?>
            <div class="ticket__warning">
                <p>Отправка комментариев доступна <br>только для зарегистрированных пользователей.</p>
                <a href="<?= Url::to(['register/index']) ?>" class="btn btn--big">Вход и регистрация</a>
            </div>
            <?php endif; ?>
            <h2 class="ticket__subtitle">Коментарии</h2>
            <?php if (!Yii::$app->user->isGuest) : ?>
            <div class="ticket__comment-form">
                <?php $form = ActiveForm::begin([
                    'id' => CommentCreateForm::class,
                    'options' => [
                        'class' => 'form comment-form'
                    ],
                    'fieldConfig' => [
                        'template' => "{input}\n{label}\n{error}",
                        'inputOptions' => ['class' => 'js-field'],
                        'errorOptions' => ['tag' => 'span', 'class' => 'error__list', 'style' => 'display: flex']
                    ]
                ])
                ?>
                    <div class="comment-form__header">
                        <a href="#" class="comment-form__avatar avatar">
                            <img
                                    src="<?= $currentUser->avatarSrc ?>"
                                    srcset="<?= $currentUser->avatarSrc ?>"
                                    alt="Аватар пользователя"
                            >
                        </a>
                        <p class="comment-form__author">Вам слово</p>
                    </div>
                    <div class="comment-form__field">
                        <?= $form->field($model, 'text', [
                            'options' => [
                                'class' => 'form__field',
                                'cols' => 30,
                                'rows' => 10
                            ]])->textarea() ?>
                    </div>
                <?php
                echo Html::submitButton('Отправить', ['class' => 'comment-form__button btn btn--white js-button']);

                ActiveForm::end()
                ?>
            </div>
            <?php endif; ?>
            <?php if (!$currentAd->comments) : ?>
                <div class="ticket__message">
                    <p>У этой публикации еще нет ни одного комментария.</p>
                </div>
            <?php else : ?>
                <div class="ticket__comments-list">
                    <ul class="comments-list">
                        <?php
                        foreach ($currentAd->getComments()->orderBy(['createAt' => SORT_DESC])->all() as $comment) :
                            ?>
                            <?= CommentWidget::widget(['comment' => $comment]) ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!Yii::$app->user->isGuest) : ?>
            <?php
            echo Html::a(
                '',
                Url::to(['offers/ajax-chat', 'adId' => $currentAd->id]),
                [
                    'class'=>'chat-button',
                    'aria-label' => 'Открыть окно чата'
                ]
            );
            ?>
        <?php endif; ?>
    </div>
</section>

<?php if (!Yii::$app->user->isGuest) : ?>
    <?php $this->beginBlock('chat'); ?>
<?php if ($isBuyer):?>
    <section class="chat visually-hidden">
        <h2 class="chat__subtitle">Чат с продавцом</h2>
        <ul class="chat__conversation">

        </ul>
        <form class="chat__form" data-chat-ref="<?=$currentRefForBuyer?>">
            <label class="visually-hidden" for="chat-field">Ваше сообщение в чат</label>
            <textarea class="chat__form-message" name="chat-message" id="chat-field" placeholder="Ваше сообщение"></textarea>
            <button class="chat__form-button" type="submit" aria-label="Отправить сообщение в чат"></button>
        </form>
    </section>
<?php else: ?>
    <section class="chat visually-hidden new-chat">
        <h2 class="chat__subtitle">Чат с покупателями</h2>

        <?php if (empty($authorChats)): ?>
            <div class="new-chat__no-have-chats">
                <p>У вас нет чатов</p>
            </div>
        <?php else: ?>
            <ul class="new-chat__list">
                <?php foreach ($authorChats as $chatId => $chat): ?>
                    <li class="new-chat__list-item" data-chat-id="<?=$chatId?>">
                        Чат с пользователем №<?=$chatId?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($authorChats)): ?>
            <?php foreach ($authorChats as $chatId => $chat): ?>
                <div class="new-chat__dialog" id="chat<?=$chatId?>" data-chat-ref="/ads/104/rooms/1/messages" data-chat-id="<?=$chatId?>">
                    <div class="new-chat__dialog-close">
                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             width="17px" height="17px" viewBox="0 0 612 612" style="enable-background:new 0 0 612 612;" xml:space="preserve" fill="#fff">
                            <g>
                                <g id="_x31_0_23_">
                                    <g>
                                        <path d="M306,0C136.992,0,0,136.992,0,306s136.992,306,306,306c168.988,0,306-137.012,306-306S475.008,0,306,0z M414.19,387.147
                                            c7.478,7.478,7.478,19.584,0,27.043c-7.479,7.478-19.584,7.478-27.043,0l-81.032-81.033l-81.588,81.588
                                            c-7.535,7.516-19.737,7.516-27.253,0c-7.535-7.535-7.535-19.737,0-27.254l81.587-81.587l-81.033-81.033
                                            c-7.478-7.478-7.478-19.584,0-27.042c7.478-7.478,19.584-7.478,27.042,0l81.033,81.033l82.181-82.18
                                            c7.535-7.535,19.736-7.535,27.253,0c7.535,7.535,7.535,19.737,0,27.253l-82.181,82.181L414.19,387.147z"/>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <h2 class="chat__subtitle">Чат с покупателям №<?=$chatId?></h2>
                    <ul class="chat__conversation">

                    </ul>
                    <form class="chat__form" data-chat-ref="ads/<?=$currentAd->id?>/rooms/<?=$chatId?>/messages">
                        <label class="visually-hidden" for="chat-field">Ваше сообщение в чат</label>
                        <textarea class="chat__form-message" name="chat-message" id="chat-field" placeholder="Ваше сообщение"></textarea>
                        <button class="chat__form-button" type="submit" aria-label="Отправить сообщение в чат"></button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif;  ?>
    </section>
<?php endif; ?>

    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-app.js";
        import { getDatabase, ref, set, get, child, push, onChildAdded, onValue, query, limitToLast} from "https://www.gstatic.com/firebasejs/9.15.0/firebase-database.js";

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyBj3tB5jizCHDUJXVTmshh1uvXP4NcJgps",
            authDomain: "buy-and-sell-f8712.firebaseapp.com",
            databaseURL: "https://buy-and-sell-f8712-default-rtdb.firebaseio.com",
            projectId: "buy-and-sell-f8712",
            storageBucket: "buy-and-sell-f8712.appspot.com",
            messagingSenderId: "251137189129",
            appId: "1:251137189129:web:0e9699b227077fb4bddfd8"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);

        let authorId = <?=$currentAd->author?>;
        let currentUserId = <?=Yii::$app->user->identity->getId()?>;
        let actualRoom;

        $('.chat__form .chat__form-button').on('click', function (e) {
           let _this = $(this);
           let $form = _this.parent('.chat__form');
           let chatRef = $form.attr('data-chat-ref');
           let messageArea = $form.find('.chat__form-message');
           let message = messageArea.val();

           if (chatRef && message) {
               $.ajax({
                   url: '/offers/add-message-to-chat/',
                   data: {
                       userId: currentUserId,
                       message: message,
                       reference: chatRef
                   },
                   method: 'POST',
               })

               messageArea.val('');
           }
        });

        <?php if ($isBuyer): ?>

            const roomChatRef = ref(database, '<?=$currentRefForBuyer?>');
            onChildAdded(roomChatRef, (snapshot) => {
                const message = snapshot.val();
                let chat = $('.chat__conversation');

                if (message.userId == currentUserId) {
                    chat.append('' +
                        '<li class="chat__message">' +
                        '<div class="chat__message-title">' +
                        '<span class="chat__message-author">Вы</span> ' +
                        '<time class="chat__message-time" datetime="2021-11-18T21:15">21:15</time> ' +
                        '</div> ' +
                        '<div class="chat__message-content">' +
                        '<p>' + message.text + '</p>' +
                        '</div>' +
                        '</li>'
                    );
                } else {
                    chat.append('' +
                        '<li class="chat__message">' +
                        '<div class="chat__message-title">' +
                        '<span class="chat__message-author">Продавец</span> ' +
                        '<time class="chat__message-time" datetime="2021-11-18T21:15">21:15</time> ' +
                        '</div> ' +
                        '<div class="chat__message-content">' +
                        '<p>' + message.text + '</p>' +
                        '</div>' +
                        '</li>'
                    );
                }
            });

        <?php else: ?>
            <?php foreach ($authorChats as $chatId => $chat): ?>
                onChildAdded(ref(database, '<?="ads/{$currentAd->id}/rooms/{$chatId}/messages"?>'), (snapshot) => {
                    const message = snapshot.val();
                    let chat = $('.chat #chat<?=$chatId?> .chat__conversation ');

                    if (message.userId == currentUserId) {
                        chat.append('' +
                            '<li class="chat__message">' +
                            '<div class="chat__message-title">' +
                            '<span class="chat__message-author">Вы</span> ' +
                            '<time class="chat__message-time" datetime="2021-11-18T21:15">21:15</time> ' +
                            '</div> ' +
                            '<div class="chat__message-content">' +
                            '<p>' + message.text + '</p>' +
                            '</div>' +
                            '</li>'
                        );
                    } else {
                        chat.append('' +
                            '<li class="chat__message">' +
                            '<div class="chat__message-title">' +
                            '<span class="chat__message-author">Покупатель</span> ' +
                            '<time class="chat__message-time" datetime="2021-11-18T21:15">21:15</time> ' +
                            '</div> ' +
                            '<div class="chat__message-content">' +
                            '<p>' + message.text + '</p>' +
                            '</div>' +
                            '</li>'
                        );
                    }
                });
            <?php endforeach; ?>
        <?php endif; ?>



    </script>
    <?php $this->endBlock(); ?>
<?php endif; ?>

<style>

    .new-chat {
        width: 350px;
        height: 450px;
        left: calc(50% + 135px);
        overflow: hidden;
        padding-bottom: 40px;
    }

    .new-chat .chat__conversation {
        height: 270px;
    }

    .new-chat .new-chat__dialog {
        position: absolute;
        width: 97%;
        height: auto;
        left: -350px;
        background-color: #2b51a6;
        top: 10px;
        transition: all .15s;
        opacity: 0;
    }

    .new-chat .new-chat__dialog.active {
        left: 5px;
        opacity: 1;
    }

    .new-chat__list {
        margin: 0;
        padding: 0;
        height: 95%;
        background-color: #fff;
        overflow: scroll;
    }

    .new-chat__list-item {
        list-style-type: none;
        margin: 0;
        padding: 0;
        border-bottom: 1px solid #2b51a6;
        padding-top: 20px;
        padding-bottom: 20px;
        padding-left: 15px;
        padding-right: 15px;
        transition: all .25s;
    }

    .new-chat__list-item:hover {
        cursor: pointer;
        background-color: #2b51a6;
        color: #ffffff;
        border-color: #fff;
    }

    .new-chat__dialog-close {
        position: absolute;
        right: 5px;
    }

    .new-chat__dialog-close:hover {
        cursor: pointer;
    }

    .new-chat__no-have-chats {
        color: #fff;
        display: flex;
        height: 100%;
        justify-content: center;
        align-items: center;
    }

</style>
<script type="module">

    $(".new-chat__list-item").on('click', function (e) {
        var _this = $(this);
        let chatid = _this.attr('data-chat-id');
        let chatIdString = "#chat" + chatid;

        let $dialog = $(chatIdString);
        if ($dialog) {
            $dialog.addClass('active');
        }
    });

    $(".new-chat__dialog-close").on('click', function (e) {
        let _this = $(this);
        let parent = _this.parent('.new-chat__dialog');

        if (parent && parent.hasClass('active')) {
            parent.removeClass('active');
        }
    });

</script>

