<?php

declare(strict_types=1);

namespace omarinina\infrastructure\models\forms;

use omarinina\domain\models\Users;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    /** @var string */
    public string $email = '';

    /** @var string */
    public string $password = '';

    /** @var Users|null */
    private ?Users $user = null;

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['email', 'validateEmail']
        ];
    }

    /**
     * @return Users|null
     */
    public function getUser(): ?Users
    {
        if ($this->user === null) {
            $email = mb_strtolower($this->email);
            $this->user = Users::findOne(['email' => $email]);
        }

        return $this->user;
    }

    /**
     * @return bool
     */
    private function isExistEmail(): bool
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            return (bool)$user;
        }
        return true;
    }

    /**
     * @param $attribute
     * @return void
     */
    public function validateEmail($attribute): void
    {
        if (!$this->isExistEmail()) {
            $this->addError($attribute, 'Неверная электронная почта');
        }
    }

    /**
     * @param $attribute
     * @return void
     */
    public function validatePassword($attribute): void
    {
        if ($this->isExistEmail() &&
            !Yii::$app->security->validatePassword($this->password, $this->getUser()->password)
        ) {
            $this->addError($attribute, 'Неправильный пароль');
            $this->password = '';
        }
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }
        return false;
    }
}
