<?php

/**
 * @var array $tasks
 * @var array $categories
 * @var Model $form
 */

use app\models\TasksForm;
use yii\base\Model;

$this->params['mainClass'] = 'main-content container';
?>

<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($tasks as $task) : ?>
        <div class="task-card">
            <div class="header-task">
                <a  href="#" class="link link--block link--big"><?= htmlspecialchars($task->title); ?></a>
                <p class="price price--task">
                    <?= !empty($task->budget)
                            ? htmlspecialchars($task->budget) . ' ₽'
                            : 'Договоримся =)';
                    ?>
                </p>
            </div>
            <p class="info-text">
                <span class="current-time"><?= $task->createdAtFormatted; ?></span>
            </p>
            <p class="task-text"><?= htmlspecialchars($task->description); ?></p>
            <div class="footer-task">
                <p class="info-text town-text">
                    <?= htmlspecialchars($task->location_name ?? 'Адрес не указан'); ?>
                </p>
                <p class="info-text category-text"><?= htmlspecialchars($task->category->name); ?></p>
                <a href="#" class="button button--black">Смотреть Задание</a>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">1</a>
            </li>
            <li class="pagination-item pagination-item--active">
                <a href="#" class="link link--page">2</a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">3</a>
            </li>
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
        </ul>
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
                                <?= htmlspecialchars($category->name); ?>
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
                                <?= $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="submit" class="button button--blue" value="Искать">
            </form>
       </div>
   </div>
</div>
