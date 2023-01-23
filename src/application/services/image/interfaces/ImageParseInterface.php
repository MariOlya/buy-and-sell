<?php

declare(strict_types=1);

namespace omarinina\application\services\image\interfaces;

use yii\web\UploadedFile;

interface ImageParseInterface
{
    public function parseImage(UploadedFile $image, bool $isAvatar): string;

    public function parseVkAvatar(string $urlAvatarVk) : string;
}
