<?php

declare(strict_types=1);

namespace omarinina\infrastructure\models\forms;

use omarinina\domain\models\Users;
use yii\base\Model;
use yii\web\UploadedFile;

class RegistrationForm extends Model
{
    /** @var string  */
    public string $name = '';

    /** @var string  */
    public string $lastName = '';

    /** @var string  */
    public string $email = '';

    /** @var string  */
    public string $password = '';

    /** @var string  */
    public string $repeatedPassword = '';

    /** @var UploadedFile  */
    public $avatar;

    public function rules(): array
    {
        return [
            [['name', 'lastName', 'email', 'password', 'repeatedPassword', 'avatar'], 'required'],
            [['name', 'lastName'], 'match', 'pattern' => '/^[A-Za-zА-Яа-яЁё\s]{2,50}$/u'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => Users::class, 'message' => 'Пользователь с таким e-mail уже существует'],
            ['password', 'string', 'min' => 6],
            ['repeatedPassword', 'compare', 'compareAttribute' => 'password'],
            ['avatar', 'image', 'extensions' => 'png, jpg', 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'lastName' => 'Фамилия',
            'email' => 'Эл. почта',
            'password' => 'Пароль',
            'repeatedPassword' => 'Пароль еще раз',
            'avatar' => 'Аватар'
        ];
    }
}
