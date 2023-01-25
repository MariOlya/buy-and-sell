<?php

declare(strict_types=1);

namespace omarinina\infrastructure\services\mail\dto;

use omarinina\domain\models\ads\Ads;
use omarinina\domain\models\Users;

class ChatMessageMailDto
{
    public function __construct(
        public readonly Ads $currentAd,
        public readonly Users $currentUser,
        public readonly string $currentTime,
        public readonly string $message,
        public readonly string $receiverEmail
    ) {
    }
}