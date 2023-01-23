<?php

declare(strict_types=1);

use app\widgets\VkButtonWidget;
use omarinina\infrastructure\models\forms\RegistrationForm;
use omarinina\infrastructure\models\forms\RegistrationVkForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var RegistrationForm|RegistrationVkForm $model */
/** @var null|array $userData */

if (!$userData) {
    $formName = RegistrationForm::class;
    $avatar = null;
} else {
    $formName = RegistrationVkForm::class;
    $model->name = $userData['first_name'] ?? '';
    $model->lastName = $userData['last_name'] ?? '';
    $model->email = $userData['email'] ?? '';
    $avatar = $userData['photo'] ?? '';
}

?>

<section class="sign-up">
    <h1 class="visually-hidden">Регистрация</h1>
        <?php $form = ActiveForm::begin([
            'id' => $formName,
            'options' => [
                'class' => 'sign-up__form form'
            ],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'inputOptions' => ['class' => 'js-field'],
                'errorOptions' => ['tag' => 'span', 'class' => 'error__list', 'style' => 'display: flex']
            ]
        ])
?>
        <div class="sign-up__title">
            <h2>Регистрация</h2>
            <?php echo Html::a('Вход', Url::to(['login/index']), ['class'=>'sign-up__link']) ?>
        </div>
        <div class="sign-up__avatar-container js-preview-container
        <?php if ($avatar) :
            ?> uploaded <?php
        endif; ?>
        ">
            <div class="sign-up__avatar js-preview">
                <?php if ($avatar) : ?>
                    <img
                            src="<?= $avatar ?>"
                            srcset="<?= $avatar ?> 2x"
                            alt=""
                    >
                <?php endif; ?>
            </div>
            <div class="sign-up__field-avatar">
                <?=$form->field($model, 'avatar', ['template' => "{input}\n{error}"])
                    ->fileInput([
                        'class' => 'visually-hidden js-file-field',
                        'id' => 'avatar'
                    ])
                ?>
                <label for="avatar">
                    <span class="sign-up__text-upload">Загрузить аватар…</span>
                    <span class="sign-up__text-another">Загрузить другой аватар…</span>
                </label>
            </div>
        </div>
        <?= $form->field($model, 'name', [
                'options' => [
                        'class' => 'form__field sign-up__field'
                ]])->textInput() ?>
        <?= $form->field($model, 'lastName', [
                'options' => [
                        'class' => 'form__field sign-up__field'
                ]])->textInput() ?>
        <?= $form->field($model, 'email', [
                'options' => [
                        'class' => 'form__field sign-up__field'
                ]])->textInput() ?>
    <?php if (!$userData) : ?>
        <?= $form->field($model, 'password', [
                'options' => [
                        'class' => 'form__field sign-up__field'
                ]])->passwordInput() ?>
        <?= $form->field($model, 'repeatedPassword', [
                'options' => [
                        'class' => 'form__field sign-up__field'
                ]])->passwordInput() ?>
    <?php endif; ?>

        <?php
            echo Html::submitButton('Создать аккаунт', ['class' => 'sign-up__button btn btn--medium js-button']);
            echo VkButtonWidget::widget();
            ActiveForm::end() ?>
</section>
