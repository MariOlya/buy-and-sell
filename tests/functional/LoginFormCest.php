<?php

use omarinina\infrastructure\models\forms\LoginForm;

class LoginFormCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage(['login/index']);
    }

    public function openLoginPage(\FunctionalTester $I): void
    {
        $I->see('Вход', 'h2');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I): void
    {
        $I->submitForm('#omarinina\infrastructure\models\forms\LoginForm', []);
        $I->expectTo('see validations errors');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I): void
    {
        $I->submitForm('#omarinina\infrastructure\models\forms\LoginForm', [
            'LoginForm[email]' => 'omarinina@xiag.ch',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Неправильный пароль');
    }

    public function loginSuccessfully(\FunctionalTester $I): void
    {
        $I->submitForm('#omarinina\infrastructure\models\forms\LoginForm', [
            'LoginForm[email]' => 'nikita@xiag.ch',
            'LoginForm[password]' => 'testtest',
        ]);
        $I->seeCurrentUrlEquals('/index-test.php');
    }
}
