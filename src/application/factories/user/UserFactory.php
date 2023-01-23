<?php

declare(strict_types=1);

namespace omarinina\application\factories\user;

use omarinina\application\factories\user\dto\NewUserDto;
use omarinina\application\factories\user\interfaces\UserFactoryInterface;
use omarinina\application\services\image\interfaces\ImageParseInterface;
use omarinina\domain\models\Users;
use Yii;
use yii\base\Exception;
use yii\web\ServerErrorHttpException;

class UserFactory implements UserFactoryInterface
{
    /**
     * @param NewUserDto $dto
     * @return Users
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function createNewUser(NewUserDto $dto): Users
    {
        $imageParse = Yii::$container->get(ImageParseInterface::class);
        $avatarSrc = $dto->form->avatar ?
            $imageParse->parseImage($dto->form->avatar, true) :
            $imageParse->parseVkAvatar($dto->vkPhoto);

        $newUser = new Users();
        $newUser->attributes = $dto->form->getAttributes();
        $newUser->avatarSrc = $avatarSrc;
        $newUser->email = mb_strtolower($dto->form->email);
        if ($dto->form->password) {
            $newUser->password = Yii::$app->getSecurity()->generatePasswordHash($dto->form->password);
        } else {
            $randomPassword = Yii::$app->security->generateRandomString(6);
            $newUser->password = Yii::$app->getSecurity()->generatePasswordHash($randomPassword);
        }
        if ($dto->vkId) {
            $newUser->vkId = (int)$dto->vkId;
        }

        if (!$newUser->save(false)) {
            throw new ServerErrorHttpException(
                'Your data has not been recorded, please try again later',
                500
            );
        }
        return $newUser;
    }
}