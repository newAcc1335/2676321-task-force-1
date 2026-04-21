<?php
/** @var Users $user */

use app\models\Users;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['mainClass'] = 'main-content container';
$this->title = Html::encode($user->name);
$rating = round($user->rating);
?>
<div class="left-column">
    <h3 class="head-main"><?= Html::encode($user->name); ?></h3>
    <div class="user-card">
        <div class="photo-rate">
            <img class="card-photo" src="<?= $user->image_url ?: '/img/man-glasses.png'; ?>"
                 width="191" height="190" alt="Фото пользователя"
                 onerror="this.src='/img/man-glasses.png'">
            <div class="card-rate">
                <div class="stars-rating big">
                    <?php for ($i = 0; $i < $rating; $i++): ?>
                        <span class="fill-star">&nbsp;</span>
                    <?php endfor; ?>
                    <?php for ($i = $rating; $i < 5; $i++): ?>
                        <span>&nbsp;</span>
                    <?php endfor; ?>
                </div>
                <span class="current-rate"><?= Yii::$app->formatter->asDecimal($user->rating, 2); ?></span>
            </div>
        </div>
        <p class="user-description">
            <?= Html::encode($user->about); ?>
        </p>
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
                <span class="age-info">30</span> лет
            </p>
        </div>
    </div>
    <h4 class="head-regular">Отзывы заказчиков</h4>
    <div class="response-card">
        <img class="customer-photo" src="/img/man-coat.png" width="120" height="127" alt="Фото заказчиков">
        <div class="feedback-wrapper">
            <p class="feedback">«Кумар сделал всё в лучшем виде. Буду обращаться к нему в
                будущем, если возникнет такая необходимость!»</p>
            <p class="task">Задание «<a href="#" class="link link--small">Повесить полочку</a>» выполнено</p>
        </div>
        <div class="feedback-wrapper">
            <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
            <p class="info-text"><span class="current-time">25 минут </span>назад</p>
        </div>
    </div>
    <div class="response-card">
        <img class="customer-photo" src="/img/man-sweater.png" width="120" height="127" alt="Фото заказчиков">
        <div class="feedback-wrapper">
            <p class="feedback">«Кумар сделал всё в лучшем виде. Буду обращаться к нему в
                будущем, если возникнет такая необходимость!»</p>
            <p class="task">Задание «<a href="#" class="link link--small">Повесить полочку</a>» выполнено</p>
        </div>
        <div class="feedback-wrapper">
            <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
            <p class="info-text"><span class="current-time">25 минут </span>назад</p>
        </div>
    </div>
</div>
<div class="right-column">
    <div class="right-card black">
        <h4 class="head-card">Статистика исполнителя</h4>
        <dl class="black-list">
            <dt>Всего заказов</dt>
            <dd><?= $user->completedTasksCount; ?> выполнено, <?= $user->failedTasksCount; ?> провалено</dd>
            <dt>Место в рейтинге</dt>
            <dd>25 место</dd>
            <dt>Дата регистрации</dt>
            <dd><?= $user->createdAtFormatted; ?></dd>
            <dt>Статус</dt>
            <dd>Открыт для новых заказов</dd>
        </dl>
    </div>
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
</div>
