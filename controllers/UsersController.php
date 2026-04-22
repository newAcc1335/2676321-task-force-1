<?php

namespace app\controllers;

use app\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $user = Users::findOne($id);

        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return $this->render('view', [
                'user' => $user
        ]);
    }
}
