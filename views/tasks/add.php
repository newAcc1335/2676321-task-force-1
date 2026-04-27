<?php

/** @var app\models\AddTaskForm $addTaskForm */
/** @var app\models\Categories[] $categories */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->params['mainClass'] = 'main-content main-content--center container';
$this->title = 'Add Task';
?>

<div class="add-task-form regular-form">
    <?php $form = ActiveForm::begin([
            'id' => 'task-form',
            'fieldConfig' => [
                    'options' => ['class' => 'form-group'],
                    'labelOptions' => ['class' => 'control-label'],
                    'errorOptions' => ['class' => 'help-block'],
            ],
    ]); ?>

    <h3 class="head-main head-main">Публикация нового задания</h3>

    <?= $form->field($addTaskForm, 'title')
            ->textInput()
            ->label('Опишите суть работы'); ?>

    <?= $form->field($addTaskForm, 'description')
            ->textarea()
            ->label('Подробности задания'); ?>

    <?= $form->field($addTaskForm, 'category_id')
            ->dropDownList(ArrayHelper::map($categories, 'id', 'name'))
            ->label('Категория'); ?>

    <?= $form->field($addTaskForm, 'location_name')
            ->textInput(['class' => 'location-icon'])
            ->label('Локация'); ?>

    <div class="half-wrapper">
        <?= $form->field($addTaskForm, 'budget')
                ->textInput(['class' => 'budget-icon'])
                ->label('Бюджет'); ?>

        <?= $form->field($addTaskForm, 'due_date')
                ->input('date')
                ->label('Срок исполнения'); ?>
    </div>

    <p class="form-label">Файлы</p>
    <div class="new-file">
        Добавить новый файл
    </div>

    <?= Html::submitInput('Опубликовать', [
            'class' => 'button button--blue'
    ]); ?>

    <?php ActiveForm::end(); ?>
</div>
