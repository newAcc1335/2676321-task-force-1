<?php

/** @var $registrationForm app\models\RegistrationForm */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Cities;

$this->params['mainClass'] = 'container container--registration';
$this->title = 'Регистрация';
?>

<div class="center-block">
    <div class="registration-form regular-form">
        <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                        'options' => ['class' => 'form-group'],
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'control-label'],
                        'errorOptions' => ['class' => 'help-block'],
                ],
        ]); ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <?= $form->field($registrationForm, 'name')
                    ->textInput(['id' => 'username'])
                    ->label('Ваше имя'); ?>
            <div class="half-wrapper">
                <?= $form->field($registrationForm, 'email')
                        ->input('email', ['id' => 'email-user'])
                        ->label('Email'); ?>

                <?= $form->field($registrationForm, 'city_id')
                        ->dropDownList(
                            Cities::find()->select(['name', 'id'])->indexBy('id')->column(),
                            ['prompt' => 'Выберите город', 'id' => 'town-user']
                        )
                        ->label('Город'); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($registrationForm, 'password')
                        ->passwordInput(['id' => 'password-user'])
                        ->label('Пароль'); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($registrationForm, 'passwordRepeat')
                        ->passwordInput(['id' => 'password-repeat-user'])
                        ->label('Повтор пароля'); ?>
            </div>
            <div class="form-group">
                <label class="control-label checkbox-label">
                    <input type="checkbox"
                           name="RegistrationForm[is_executor]"
                           value="1"
                           id="response-user"
                            <?= $registrationForm->is_executor ? 'checked' : ''; ?>
                    >
                    я собираюсь откликаться на заказы
                </label>
            </div>
            <?= Html::submitInput('Создать аккаунт', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
