<?php

/**
 * @var ActiveDataProvider $provider
 * @var Categories $categories
 * @var TasksForm $form
 */

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TasksForm;
use app\models\Categories;
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
                            : 'Договоримся =)'; ?>
                </p>
            </div>
            <p class="info-text">
                <span class="current-time"><?= $task->createdAtFormatted; ?></span>
            </p>
            <p class="task-text"><?= Html::encode($task->description); ?></p>
            <div class="footer-task">
                <p class="info-text town-text">
                    <?= Html::encode($task->location_name ?? 'Адрес не указан'); ?>
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
            <form method="get">
                <h4 class="head-card"><?= $form->getAttributeLabel('categories'); ?></h4>
                <?php foreach ($categories as $category): ?>
                    <div class="form-group">
                        <div class="checkbox-wrapper">
                            <label class="control-label" for="<?= $category->id; ?>">
                                <input type="checkbox"
                                       id="<?= $category->id; ?>"
                                       name="TasksForm[categories][]"
                                       value="<?= $category->id; ?>"
                                        <?= in_array($category->id, $form->categories) ? 'checked' : ''; ?>
                                >
                                <?= Html::encode($category->name); ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>

                <h4 class="head-card">Дополнительно</h4>
                <div class="form-group">
                    <label class="control-label" for="without-performer">
                        <input type="hidden" name="TasksForm[isWithoutExecutor]" value="0">
                        <input
                                id="without-performer"
                                type="checkbox"
                                name="TasksForm[isWithoutExecutor]"
                                value="1"
                                <?= $form->isWithoutExecutor ? 'checked' : ''; ?>
                        >
                        <?= $form->getAttributeLabel('isWithoutExecutor'); ?>
                    </label>
                </div>
                <h4 class="head-card"><?= $form->getAttributeLabel('period'); ?></h4>
                <div class="form-group">
                    <label for="period-value"></label>
                    <select id="period-value" name="TasksForm[period]">
                        <?php foreach (TasksForm::PERIOD_OPTIONS as $value => $label): ?>
                            <option value="<?= $value; ?>" <?= (string)$form->period === (string)$value ? 'selected' : ''; ?>>
                                <?= Html::encode($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="submit" class="button button--blue" value="Искать">
            </form>
       </div>
   </div>
</div>
