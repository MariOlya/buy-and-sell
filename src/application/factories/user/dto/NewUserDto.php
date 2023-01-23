<?php

declare(strict_types=1);

namespace omarinina\application\factories\user\dto;

use omarinina\infrastructure\models\forms\RegistrationForm;
use omarinina\infrastructure\models\forms\RegistrationVkForm;

class NewUserDto
{
    public function __construct(
        public readonly RegistrationForm|RegistrationVkForm $form,
        public readonly ?string $vkId = null,
        public readonly ?string $vkPhoto = null
    ) {
    }
}
