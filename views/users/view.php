<?php
/** @var Users $user */

use app\widgets\StarsWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Users;

$this->params['mainClass'] = 'main-content container';
$this->title = $user->name;
?>
<div class="left-column">
    <h3 class="head-main"><?= Html::encode($user->name); ?></h3>
    <div class="user-card">
        <div class="photo-rate">
            <img class="card-photo" src="<?= Html::encode($user->image_url ?: '/img/man-glasses.png'); ?>"
                 width="191" height="190" alt="Фото пользователя"
            >
            <div class="card-rate">
                <?= StarsWidget::widget(['rating' => $user->rating, 'size' => 'big']); ?>
                <span class="current-rate"><?= Yii::$app->formatter->asDecimal($user->rating, 2); ?></span>
            </div>
        </div>
        <p class="user-description"><?= Html::encode($user->about ?? ''); ?></p>
    </div>

    <div class="specialization-bio">
        <div class="specialization">
            <p class="head-info">Специализации</p>
            <ul class="special-list">
                <?php foreach ($user->categories as $category): ?>
                    <li class="special-item">
                        <a href="<?= Url::to([
                                'tasks/index',
                                'TasksForm' => [
                                        'categories' => [$category->id]
                                ]
                        ]) ?>" class="link link--regular">
                            <?= Html::encode($category->name) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bio">
            <p class="head-info">Био</p>
            <p class="bio-info">
                <span class="country-info">Россия</span>,
                <span class="town-info"><?= Html::encode($user->city->name ?? '') ?></span>,
                <?php if ($user->birthday): ?>
                    <span class="age-info"><?= $user->age; ?></span> лет
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php if (!empty($user->executorReviews)): ?>
        <h4 class="head-regular">Отзывы заказчиков</h4>

        <?php foreach ($user->executorReviews as $review): ?>
            <div class="response-card">
                <img class="customer-photo"
                     src="<?= Html::encode($review->author->image_url ?? '/img/man-glasses.png') ?>"
                     width="120" height="127" alt="Фото заказчика">
                <div class="feedback-wrapper">
                    <p class="feedback">«<?= Html::encode($review->comment); ?>»</p>
                    <p class="task">
                        Задание «<a href="<?= Url::to(['tasks/view', 'id' => $review->task_id]) ?>"
                                    class="link link--small">
                            <?= Html::encode($review->task->title) ?>
                        </a>» выполнено
                    </p>
                </div>

                <div class="feedback-wrapper">
                    <?= StarsWidget::widget(['rating' => $review->rating, 'size' => 'small']) ?>
                    <p class="info-text">
                        <span class="current-time">
                            <?= Yii::$app->formatter->asRelativeTime($review->created_at) ?>
                        </span>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="right-column">
    <div class="right-card black">
        <h4 class="head-card">Статистика исполнителя</h4>
        <dl class="black-list">
            <dt>Всего заказов</dt>
            <dd><?= $user->completedTasksCount; ?> выполнено, <?= $user->failedTasksCount; ?> провалено</dd>
            <dt>Место в рейтинге</dt>
            <dd><?= $user->rank; ?> место</dd>
            <dt>Дата регистрации</dt>
            <dd><?= $user->createdAtFormatted; ?></dd>
            <dt>Статус</dt>
            <dd><?= $user->isActiveExecutor ? 'Занят' : 'Открыт для новых заказов'; ?></dd>
        </dl>
    </div>
    <?php if ($user->isContactVisible(Yii::$app->user->id)): ?>
        <div class="right-card white">
            <h4 class="head-card">Контакты</h4>
            <ul class="enumeration-list">
                <li class="enumeration-item">
                    <a href="tel:<?= Html::encode($user->phone); ?>" class="link link--block link--phone"><?= Html::encode($user->phone); ?></a>
                </li>
                <li class="enumeration-item">
                    <a href="mailto:<?= Html::encode($user->email); ?>" class="link link--block link--email"><?= Html::encode($user->email); ?></a>
                </li>
                <li class="enumeration-item">
                    <a href="<?= $user->tgUrl; ?>" class="link link--block link--tg"><?= Html::encode($user->tg); ?></a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</div>
