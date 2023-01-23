<?php

declare(strict_types=1);

namespace unit;

use omarinina\infrastructure\models\forms\LoginForm;
use Yii;

class LoginFormTest extends \Codeception\Test\Unit
{
    private $model;

    protected function _after(): void
    {
        \Yii::$app->user->logout();
    }

    public function testLoginNoUser(): void
    {
        $this->model = new LoginForm([
            'email' => 'not_existing_email',
            'password' => 'not_existing_password',
        ]);

        $this->assertFalse($this->model->login());
        $this->assertTrue(Yii::$app->user->isGuest);
    }

    public function testLoginWrongPassword()
    {
        $this->model = new LoginForm([
            'email' => 'omarinina@xiag.ch',
            'password' => 'wrong_password',
        ]);

        $this->assertFalse($this->model->login());
        $this->assertTrue(Yii::$app->user->isGuest);
        $this->assertArrayHasKey('password', $this->model->errors, 'Неправильный пароль');
    }

    public function testLoginCorrect()
    {
        $this->model = new LoginForm([
            'email' => 'nikita@xiag.ch',
            'password' => 'testtest',
        ]);

        $this->assertTrue($this->model->login());
        $this->assertFalse(Yii::$app->user->isGuest);
        $this->assertArrayNotHasKey('password', $this->model->errors);
    }
}