<?php

declare(strict_types=1);

namespace omarinina\application\services\user;

use omarinina\application\services\user\interfaces\UserAuthVkInterface;
use yii\authclient\clients\VKontakte;

class UserAuthVkService implements UserAuthVkInterface
{
    /**
     * @param string $code
     * @param VKontakte $vkClient
     * @return VKontakte
     * @throws \yii\web\HttpException
     */
    public function applyAccessTokenForVk(string $code, VKontakte $vkClient): VKontakte
    {
        $token = $vkClient->fetchAccessToken($code);
        $requestOAuth = $vkClient->createRequest();

        $vkClient->applyAccessTokenToRequest($requestOAuth, $token);

        return $vkClient;
    }
}