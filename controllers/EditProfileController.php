<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Cities;
use app\models\EditProfileForm;
use app\models\SecurityForm;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Страница «Настройки аккаунта». Содержит две вкладки: «Мой профиль» и «Безопасность».
 *
 */
class EditProfileController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Вкладка «Мой профиль».
     *
     * @throws Throwable
     */
    public function actionIndex(): Response|string
    {
        $user = Yii::$app->user->identity;
        $form = new EditProfileForm();

        if ($form->load(Yii::$app->request->post())) {
            $form->avatar = UploadedFile::getInstance($form, 'avatar');

            if ($form->validate()) {
                $form->update($user);
                Yii::$app->session->setFlash('success', 'Профиль обновлён');

                return $this->redirect(['users/view', 'id' => $user->id]);
            }
        } else {
            $form->loadFromUser($user);
        }

        return $this->render('index', [
            'categories' => Categories::find()->all(),
            'cities' => Cities::find()->all(),
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Вкладка «Безопасность».
     *
     * @throws Exception
     */
    public function actionSecurity(): Response|string
    {
        $user = Yii::$app->user->identity;
        $form = new SecurityForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->update($user);
            Yii::$app->session->setFlash('success', 'Настройки сохранены');

            return $this->redirect(['users/view', 'id' => $user->id]);
        }

        if (!Yii::$app->request->isPost) {
            $form->loadFromUser($user);
        }

        return $this->render('security', [
            'form' => $form,
        ]);
    }
}
