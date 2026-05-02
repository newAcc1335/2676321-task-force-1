<?php

namespace app\controllers;

use app\models\Cities;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Response;
use app\models\Users;
use app\models\RegistrationForm;

/**
 * Регистрация нового пользователя.
 */
class RegistrationController extends Controller
{
    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionIndex(): Response|string
    {
        $registrationForm = new RegistrationForm();

        if ($registrationForm->load(Yii::$app->request->post()) && $registrationForm->validate()) {
            $user = new Users();
            $user->name = $registrationForm->name;
            $user->email = $registrationForm->email;
            $user->city_id = $registrationForm->city_id;
            $user->role = $registrationForm->is_executor ? Users::ROLE_EXECUTOR : Users::ROLE_AUTHOR;
            $user->password_hash = Yii::$app->security->generatePasswordHash($registrationForm->password);

            if ($user->save()) {
                return $this->redirect(['/tasks/index']);
            }
        }

        $cities = Cities::find()->select(['name', 'id'])->indexBy('id')->column();

        return $this->render('index', [
                'registrationForm' => $registrationForm,
                'cities' => $cities,
        ]);
    }
}
