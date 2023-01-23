<?php

declare(strict_types=1);

namespace app\controllers;

use omarinina\infrastructure\models\forms\LoginForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class LoginController extends Controller
{
    /**
     * @return string|Response
     */
    public function actionIndex(): string|Response
    {
        $loginForm = new LoginForm();

        if (Yii::$app->request->getIsPost()) {
            if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
                return $this->goHome();
            }
        }

        return $this->render('index', [
            'model' => $loginForm
        ]);
    }
}