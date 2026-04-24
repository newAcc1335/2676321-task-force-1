<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\models\LoginForm;

class LandingController extends Controller
{
    public $layout = 'landing';

    public function actionIndex(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/tasks']);
        }

        return $this->render('index');
    }

    public function actionLogin(): Response|array
    {
        $form = new LoginForm();

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            Yii::$app->user->login($form->getUser());
            return $this->redirect(['/tasks']);
        }

        return $this->redirect(['/landing']);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
