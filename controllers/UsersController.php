<?php

namespace app\controllers;

use app\models\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Профиль пользователя, который является исполнителем.
 */
class UsersController extends Controller
{
    public function actionView(int $id): string
    {
        $user = Users::findOne($id);

        if (!$user || !$user->isRoleExecutor()) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        return $this->render('view', [
                'user' => $user
        ]);
    }
}
