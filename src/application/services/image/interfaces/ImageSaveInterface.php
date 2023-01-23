<?php

declare(strict_types=1);

namespace omarinina\application\services\image\interfaces;

interface ImageSaveInterface
{
    public function saveNewImage(string $imageSrc): int;
}
