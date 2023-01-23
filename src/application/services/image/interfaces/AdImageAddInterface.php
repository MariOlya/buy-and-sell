<?php

declare(strict_types=1);

namespace omarinina\application\services\image\interfaces;

interface AdImageAddInterface
{
    public function addAdImages(array $images, int $adId): bool;
}
