<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\models\LoginForm;

class LandingController extends Controller
{
    public $layout = 'landing';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): array|Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/tasks']);
        }

        $formModel = new LoginForm();

        if ($formModel->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($formModel);
            }
            if ($formModel->validate() && Yii::$app->user->login($formModel->getUser())) {
                return $this->redirect(['/tasks']);
            }
        }

        return $this->render('index', ['loginForm' => $formModel]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
