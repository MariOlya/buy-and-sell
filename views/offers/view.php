<?php

declare(strict_types=1);

use app\widgets\CommentWidget;
use omarinina\domain\models\ads\Ads;
use omarinina\domain\models\ads\Comments;
use omarinina\domain\models\Users;
use omarinina\infrastructure\models\forms\CommentCreateForm;
use omarinina\infrastructure\constants\AdConstants;
use omarinina\infrastructure\constants\FirebaseConfigForJS;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Ads $currentAd */
/** @var CommentCreateForm $model */
/** @var Comments $comments */
/** @var array $authorChats */

/** @var Users $currentUser */
$currentUser = Yii::$app->user->identity;

$isNotAuthor = Yii::$app->user->id !== $currentAd->author;
$isAuthorSeller = $currentAd->type->name === AdConstants::TYPE_SELL;

if ($currentUser && $isNotAuthor) {
    $currentRefForNotAuthor = "ads/{$currentAd->id}/rooms/{$currentUser->id}/messages";
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
            <?php if (!$comments) : ?>
                <div class="ticket__message">
                    <p>У этой публикации еще нет ни одного комментария.</p>
                </div>
            <?php else : ?>
                <div class="ticket__comments-list">
                    <ul class="comments-list">
                        <?php
                        foreach ($comments as $comment) :
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

    <?php if ($isNotAuthor) : ?>
    <section class="chat visually-hidden">
        <h2 class="chat__subtitle">
            <?= $isAuthorSeller ? 'Чат с продавцом' : 'Чат с покупателем' ?>
        </h2>
        <ul class="chat__conversation">
            </ul>
        <div class="chat__form" data-chat-ref="<?= $currentRefForNotAuthor ?>">
            <label class="visually-hidden" for="chat-field">Ваше сообщение в чат</label>
            <textarea class="chat__form-message" id="chat-field" placeholder="Ваше сообщение"></textarea>
            <button class="chat__form-button" type="submit" aria-label="Отправить сообщение в чат"></button>
        </div>
    </section>
    <?php else : ?>
    <section class="chat visually-hidden new-chat">
        <h2 class="chat__subtitle">Чат с <?= $isAuthorSeller ? 'покупателями' : 'продавцами' ?></h2>

        <?php if (empty($authorChats)) : ?>
            <div class="new-chat__no-have-chats">
                <p>У вас нет чатов</p>
            </div>
        <?php else : ?>
            <ul class="new-chat__list">
                <?php foreach ($authorChats as $chatId => $chat) : ?>
                    <li class="new-chat__list-item" data-chat-id="<?=$chatId?>">
                        Чат с
                        <?= $isAuthorSeller ? ' покупателем ' : ' продавцом ' ?>
                        №<?=$chatId?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($authorChats)) : ?>
            <?php foreach ($authorChats as $chatId => $chat) : ?>
                <div class="new-chat__dialog" id="chat<?=$chatId?>" data-chat-id="<?=$chatId?>">
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
                    <h2 class="chat__subtitle">
                        Чат с
                        <?= $isAuthorSeller ? ' покупателем ' : ' продавцом ' ?>
                        №<?=$chatId?>
                    </h2>
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
        import { getDatabase, ref, onChildAdded} from "https://www.gstatic.com/firebasejs/9.15.0/firebase-database.js";

        // Your web app's Firebase configuration
        const firebaseConfig = <?= FirebaseConfigForJS::getFirebaseConfig() ?>;

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
                        message: message,
                        reference: chatRef,
                        currentAdId: <?= $currentAd->id ?>,
                        receiverId: <?= $chatId ?? 0 ?>
                    },
                    method: 'POST',
                })

                messageArea.val('');
            }
        });

        function timeConvert(timestamp) {
            const fullDate = new Date(timestamp * 1000);
            const hours = fullDate.getHours();
            const minutes = "0" + fullDate.getMinutes();
            return hours + ':' + minutes.slice(-2);
        }

        <?php if ($isNotAuthor) : ?>
        const roomChatRef = ref(database, '<?=$currentRefForNotAuthor?>');
        onChildAdded(roomChatRef, (snapshot) => {
            const message = snapshot.val();
            let chat = $('.chat__conversation');
            let role;

            if (message.userId === currentUserId) {
                role = 'Вы';
            } else {
                role = '<?= $isAuthorSeller ? 'Продавец' : 'Покупатель' ?>';
            }

            chat.append('' +
                '<li class="chat__message">' +
                '<div class="chat__message-title">' +
                '<span class="chat__message-author">' + role + '</span> ' +
                '<time class="chat__message-time">'
                + timeConvert(message.createAt) +
                '</time> ' +
                '</div> ' +
                '<div class="chat__message-content">' +
                '<p>' + message.text + '</p>' +
                '</div>' +
                '</li>'
            );

        });

        <?php else : ?>
            <?php foreach ($authorChats as $chatId => $chat) : ?>
        onChildAdded(ref(database, '<?="ads/{$currentAd->id}/rooms/{$chatId}/messages"?>'), (snapshot) => {
            const message = snapshot.val();
            let chat = $('.chat #chat<?=$chatId?> .chat__conversation ');
            let role;

            if (message.userId === currentUserId) {
                role = 'Вы';
            } else {
                role = '<?= $isAuthorSeller ? 'Покупатель' : 'Продавец' ?>';
            }

            chat.append('' +
                '<li class="chat__message">' +
                '<div class="chat__message-title">' +
                '<span class="chat__message-author">' + role + '</span> ' +
                '<time class="chat__message-time">'
                + timeConvert(message.createAt) +
                '</time> ' +
                '</div> ' +
                '<div class="chat__message-content">' +
                '<p>' + message.text + '</p>' +
                '</div>' +
                '</li>'
            );

        });
            <?php endforeach; ?>
        <?php endif; ?>

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

    <?php $this->endBlock(); ?>
<?php endif; ?>

