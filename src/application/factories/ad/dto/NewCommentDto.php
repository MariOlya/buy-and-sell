<?php

declare(strict_types=1);

namespace omarinina\application\factories\ad\dto;

use omarinina\infrastructure\models\forms\CommentCreateForm;

class NewCommentDto
{
    public function __construct(
        public readonly CommentCreateForm $form,
        public readonly int $author,
        public readonly int $adId
    ) {
    }
}
