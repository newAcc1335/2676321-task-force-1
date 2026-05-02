<?php

/** @var Tasks[] $tasks */
/** @var string $status */
/** @var bool $isAuthor */
/** @var array $statusFilters */
/** @var string $sectionTitle */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Tasks;

$this->params['mainClass'] = 'main-content container';
$this->title = 'My Tasks';
?>

<div class="left-menu">
    <h3 class="head-main head-task">Мои задания</h3>
    <ul class="side-menu-list">
        <?php foreach ($statusFilters as $filter): ?>
            <li class="side-menu-item <?= $filter['active'] ? 'side-menu-item--active' : ''; ?>">
                <a href="<?= Url::to(['my-tasks/index', 'status' => $filter['status']]); ?>"
                   class="link link--nav">
                    <?= Html::encode($filter['label']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="left-column left-column--task">
    <h3 class="head-main head-regular">
        <?= Html::encode($sectionTitle); ?>
    </h3>

    <?php if (empty($tasks)): ?>
        <p class="info-text">Заданий нет.</p>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
            <div class="task-card">
                <div class="header-task">
                    <a href="<?= Url::to(['tasks/view', 'id' => $task->id]); ?>"
                       class="link link--block link--big">
                        <?= Html::encode($task->title); ?>
                    </a>
                    <?php if ($task->budget): ?>
                        <p class="price price--task"><?= Html::encode($task->budget); ?> ₽</p>
                    <?php else: ?>
                        <p class="price price--task">Договоримся =)</p>
                    <?php endif; ?>
                </div>

                <p class="info-text">
                    <span class="current-time"><?= $task->createdAtFormatted; ?></span>
                </p>

                <p class="task-text"><?= Html::encode($task->description); ?></p>

                <div class="footer-task">
                    <p class="info-text town-text">
                        <?= Html::encode($task->location_name ?? 'Адрес не указан'); ?>
                    </p>
                    <p class="info-text category-text">
                        <?= Html::encode($task->category->name ?? ''); ?>
                    </p>
                    <a href="<?= Url::to(['tasks/view', 'id' => $task->id]); ?>" class="button button--black">
                        Смотреть задание
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>