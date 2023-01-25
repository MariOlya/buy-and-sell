<?php

declare(strict_types=1);

namespace app\controllers;

use omarinina\domain\models\Users;
use omarinina\infrastructure\services\user\interfaces\UserAuthVkInterface;
use Yii;
use yii\authclient\clients\VKontakte;
use yii\authclient\Collection;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthController extends Controller
{
    /** @var UserAuthVkInterface */
    private UserAuthVkInterface $userAuthVk;

    public function __construct(
        $id,
        $module,
        UserAuthVkInterface $userAuthVk,
        $config = []
    ) {
        $this->userAuthVk = $userAuthVk;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return Response|string
     * @throws InvalidConfigException|NotFoundHttpException
     */
    public function actionAuthorizeUserViaVk() : Response|string
    {
        /** @var Collection $collectionClientsOAuth */
        $collectionClientsOAuth = Yii::$app->get('authClientCollection');
        /** @var VKontakte $vkClientOAuth */
        $vkClientOAuth = $collectionClientsOAuth->getClient('vkontakte');

        $codeVk = Yii::$app->request->get('code');
        $userData = $this->userAuthVk->applyAccessTokenForVk($codeVk, $vkClientOAuth)->getUserAttributes();

        if (!$userData) {
            throw new NotFoundHttpException();
        }

        $currentUser = Users::findOne(['vkId' => $userData['id']]);

        if (!$currentUser) {
            if (array_key_exists('email', $userData)) {
                $currentUser = Users::findOne(['email' => mb_strtolower($userData['email'])]);
                $currentUser?->addVkId($userData['id']);
            }
            if (!$currentUser) {
                return $this->redirect([
                    'register/index',
                    'userData' => $userData
                ]);
            }
        }

        Yii::$app->user->login($currentUser);
        return $this->redirect(['/']);
    }

    /**
     * @param int $userId
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionLogin(int $userId) : Response|string
    {
        $newUser = Users::findOne($userId);
        if (!$newUser) {
            throw new NotFoundHttpException('User is not found', 404);
        }

        Yii::$app->user->login($newUser);
        return $this->redirect(['/']);
    }
}