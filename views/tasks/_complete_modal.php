<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var app\models\CompleteTaskForm $completeForm */
/** @var app\models\Tasks $task */

$this->registerCss("
    .stars-radio { display: flex; flex-direction: row-reverse; justify-content: flex-end; margin-bottom: 15px; }
    .stars-radio input[type='radio'] { display: none; }
    .stars-radio label span { cursor: pointer; }
    .stars-radio input[type='radio']:checked ~ label span,
    .stars-radio label:hover span,
    .stars-radio label:hover ~ label span { background-image: url('/img/star-fill.svg'); }
");
?>

<section class="pop-up pop-up--complete <?= $completeForm->hasErrors() ? 'pop-up--open' : 'pop-up--close'; ?>">
    <div class="pop-up--wrapper">
        <h4>Завершение задания</h4>

        <p class="pop-up-text">
            Вы собираетесь отметить это задание как выполненное.
            Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если возникли проблемы.
        </p>

        <?php $form = ActiveForm::begin([
            'action' => ['tasks/complete-task', 'id' => $task->id],
            'options' => ['class' => 'completion-form pop-up--form regular-form'],
        ]); ?>

        <?= $form->field($completeForm, 'comment', [
                'options' => ['class' => 'form-group'],
            ])->textarea()
            ->label('Ваш комментарий', ['class' => 'control-label']); ?>

        <p class="completion-head control-label">
            Оценка работы
        </p>

        <div class="stars-radio stars-rating big">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <?= Html::radio('CompleteTaskForm[rating]', $i === 5, [
                        'value' => $i,
                        'id' => "star-$i",
                ]) ?>
                <label for="star-<?= $i ?>"><span>&nbsp;</span></label>
            <?php endfor; ?>
        </div>

        <?= Html::submitInput('Завершить', [
            'class' => 'button button--pop-up button--blue'
        ]) ?>

        <?php ActiveForm::end(); ?>

        <div class="button-container">
            <?= Html::a('Закрыть окно', '#', [
                'class' => 'button--close',
            ]) ?>
        </div>
    </div>
</section>
