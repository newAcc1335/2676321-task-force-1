<?php

/**
 * @var ActiveDataProvider $provider
 * @var Categories $categories
 * @var TasksForm $form
 */

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TasksForm;
use app\models\Categories;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->params['mainClass'] = 'main-content container';
$this->title = 'Задания';
?>

<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($provider->getModels() as $task) : ?>
        <div class="task-card">
            <div class="header-task">
                <a  href="<?= Url::to(['tasks/view', 'id' => $task->id]) ?>" class="link link--block link--big">
                    <?= Html::encode($task->title); ?>
                </a>
                <p class="price price--task">
                    <?= !empty($task->budget)
                            ? Html::encode($task->budget) . ' ₽'
                            : 'Договорная'; ?>
                </p>
            </div>
            <p class="info-text">
                <span class="current-time"><?= $task->createdAtFormatted; ?></span>
            </p>
            <p class="task-text"><?= Html::encode($task->description); ?></p>
            <div class="footer-task">

                <p class="info-text town-text">
                    <?= Html::encode($task->city?->name ?? 'Адрес не указан');?>
                </p>
                <p class="info-text category-text"><?= Html::encode($task->category->name); ?></p>
                <a href="<?= Url::to(['tasks/view', 'id' => $task->id]) ?>" class="button button--black">
                    Смотреть Задание
                </a>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="pagination-wrapper">
        <?= LinkPager::widget([
                'pagination' => $provider->getPagination(),
                'options' => ['class' => 'pagination-list'],
                'linkOptions' => ['class' => 'link link--page'],
                'linkContainerOptions' => ['class' => 'pagination-item'],
                'activePageCssClass' => 'pagination-item--active',
                'prevPageLabel' => '',
                'nextPageLabel' => '',
                'prevPageCssClass' => 'pagination-item mark',
                'nextPageCssClass' => 'pagination-item mark',
        ]); ?>
    </div>
</div>

<div class="right-column">
   <div class="right-card black">
       <div class="search-form">
           <?php $activeForm = ActiveForm::begin([
                   'method' => 'get',
                   'options' => ['id' => 'filter-form'],
                   'action' => ['/tasks/index'],
           ]); ?>
           <h4 class="head-card"><?= $form->getAttributeLabel('categories'); ?></h4>
           <?= $activeForm->field($form, 'categories', [
                   'template' => '{input}',
               ])->checkboxList(
                   ArrayHelper::map($categories, 'id', 'name'),
                   [
                       'item' => function ($index, $label, $name, $checked, $value) {
                           return '<div class="form-group"><div class="checkbox-wrapper">'
                               . '<label class="control-label">'
                               . Html::checkbox($name, $checked, ['value' => $value, 'id' => 'category-' . $value])
                               . Html::encode($label)
                               . '</label></div></div>';
                       },
                   ]
               ); ?>

           <h4 class="head-card">Дополнительно</h4>

           <?php foreach (['isRemote', 'isWithoutResponses'] as $attr): ?>
               <?= $activeForm->field($form, $attr, [
                       'template' => '<div class="form-group"><label class="control-label">{input} {label}</label></div>',
                       'options' => ['tag' => false],
               ])->checkbox(['uncheck' => '0'], false) ?>
           <?php endforeach; ?>

           <h4 class="head-card"><?= $form->getAttributeLabel('period'); ?></h4>

           <?= $activeForm->field($form, 'period', [
                   'template' => '<div class="form-group">{input}</div>',
                   'options' => ['tag' => false],
           ])->dropDownList(TasksForm::PERIOD_OPTIONS) ?>

           <?= Html::submitInput('Искать', ['class' => 'button button--blue']) ?>
           <?php ActiveForm::end(); ?>
       </div>
   </div>
</div>
