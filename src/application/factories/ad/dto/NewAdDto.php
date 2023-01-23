<?php

declare(strict_types=1);

namespace omarinina\application\factories\ad\dto;

use omarinina\infrastructure\models\forms\AdCreateForm;

class NewAdDto
{
    public function __construct(public readonly AdCreateForm $form, public readonly int $author)
    {
    }
}