<?php

/** @var SecurityForm $form */

use app\models\SecurityForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->params['mainClass'] = 'main-content main-content--left container';
$this->title = 'Настройки Безопасности';
?>

<?= $this->render('_left_menu', ['activeTab' => 'security']) ?>

<div class="my-profile-form">
    <?php $activeForm = ActiveForm::begin([
        'enableClientValidation' => false,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'control-label'],
            'errorOptions' => ['class' => 'help-block', 'tag' => 'span'],
        ],
    ]); ?>

    <h3 class="head-main head-regular">Безопасность</h3>

    <?= $activeForm->field($form, 'oldPassword')->passwordInput() ?>

    <div class="half-wrapper">
        <?= $activeForm->field($form, 'newPassword')->passwordInput() ?>
        <?= $activeForm->field($form, 'newPasswordRepeat')->passwordInput() ?>
    </div>

    <?php if (Yii::$app->user->identity->isRoleExecutor()): ?>
        <?= $activeForm->field($form, 'is_customer_only')->checkbox() ?>
    <?php endif; ?>

    <?= Html::submitButton('Сохранить', ['class' => 'button button--blue']) ?>

    <?php ActiveForm::end(); ?>
</div>