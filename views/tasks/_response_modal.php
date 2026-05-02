<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var app\models\ResponseForm $responseForm */
/** @var app\models\Tasks $task */

?>

<section class="pop-up pop-up--respond pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Добавление отклика</h4>
        <p class="pop-up-text">
            Вы собираетесь оставить свой отклик к этому заданию.
            Пожалуйста, укажите стоимость работы и добавьте комментарий, если необходимо.
        </p>

        <?php $form = ActiveForm::begin([
            'action' => ['tasks/respond-task', 'id' => $task->id],
            'options' => ['class' => 'addition-form pop-up--form regular-form'],
        ]); ?>

        <?= $form->field($responseForm, 'comment', [
                'options' => ['class' => 'form-group'],
            ])->textarea()
                ->label('Ваш комментарий', ['class' => 'control-label']); ?>

        <?= $form->field($responseForm, 'price', [
                'options' => ['class' => 'form-group'],
            ])->textInput()
                ->label('Стоимость', ['class' => 'control-label']); ?>

        <?= Html::submitInput('Отправить', [
            'class' => 'button button--pop-up button--blue'
        ]) ?>

        <?php ActiveForm::end(); ?>

        <div class="button-container">
            <?= Html::a('Закрыть окно', ['tasks/view', 'id' => $task->id], [
                'class' => 'button--close'
            ]) ?>
        </div>
    </div>
</section>
