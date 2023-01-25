<?php

declare(strict_types=1);

namespace omarinina\infrastructure\services\user\interfaces;

use yii\authclient\clients\VKontakte;

interface UserAuthVkInterface
{
    /**
     * @param string $code
     * @param VKontakte $vkClient
     * @return VKontakte
     */
    public function applyAccessTokenForVk(string $code, VKontakte $vkClient) : VKontakte;
}