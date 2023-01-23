<?php

declare(strict_types=1);

namespace omarinina\infrastructure\models\forms;

use omarinina\domain\models\Users;

class RegistrationVkForm extends RegistrationForm
{
    public function rules(): array
    {
        return [
            [['name', 'lastName', 'email'], 'required'],
            [['name', 'lastName'], 'match', 'pattern' => '/^[A-Za-zА-Яа-яЁё\s]{2,50}$/u'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => Users::class, 'message' => 'Пользователь с таким e-mail уже существует'],
            ['avatar', 'image', 'extensions' => 'png, jpg', 'maxSize' => 5 * 1024 * 1024],
        ];
    }
}
