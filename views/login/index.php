<?php

declare(strict_types=1);

use omarinina\infrastructure\models\forms\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LoginForm $model */

?>
<section class="login">
    <h1 class="visually-hidden">Логин</h1>
    <?php $form = ActiveForm::begin([
        'id' => LoginForm::class,
        'options' => [
            'class' => 'login__form form'
        ],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'inputOptions' => ['class' => 'js-field'],
            'errorOptions' => ['tag' => 'span', 'class' => 'error__list', 'style' => 'display: flex']
        ]
    ])
    ?>
        <div class="login__title">
            <?php echo Html::a('Регистрация', 'register/index', ['class'=>'login__link']) ?>
            <h2>Вход</h2>
        </div>
        <?= $form->field($model, 'email', [
            'options' => [
                'class' => 'form__field login__field'
            ]])->textInput() ?>
        <?= $form->field($model, 'password', [
            'options' => [
                'class' => 'form__field login__field'
            ]])->passwordInput() ?>

        <?php
        echo Html::submitButton('Войти', ['class' => 'login__button btn btn--medium js-button']);
        echo Html::a('Войти через <span class="icon icon--vk"></span>', '#', ['class'=>'btn btn--small btn--flex btn--white']);

        ActiveForm::end() ?>
</section>