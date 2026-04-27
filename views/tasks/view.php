<?php

/** @var Tasks $task */

use app\models\Tasks;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['mainClass'] = 'main-content container';
$this->title = 'my task =)';
?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= Html::encode($task->title) ?></h3>

        <?php if ($task->budget): ?>
            <p class="price price--big"><?= $task->budget ?> ₽</p>
        <?php endif; ?>
    </div>

    <p class="task-description"><?= Html::encode($task->description); ?></p>
    <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
    <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
    <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
    <div class="task-map">
        <img class="map" src="/img/map.png" width="725"
             height="346" alt="<?= Html::encode($task->location_name ?? ''); ?>">
        <p class="map-address town"><?= Html::encode($task->city->name ?? ''); ?></p>
        <p class="map-address"><?= Html::encode($task->location_name ?? ''); ?></p>
    </div>
    <h4 class="head-regular">Отклики на задание</h4>

    <?php foreach ($task->responses as $response): ?>
        <div class="response-card">
            <img class="customer-photo" src=" <?=Html::encode($response->executor->image_url); ?>" width="146" height="156" alt="Фото заказчиков">
            <div class="feedback-wrapper">
                <a href="#" class="link link--block link--big">
                    <?= Html::encode($response->executor->name ?? 'Безымянный'); ?>
                </a>

                <!-- это потом заменить из users/view, надо выделить в отдельную штуку-->
                <div class="response-wrapper">
                    <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
                    <p class="reviews">2 отзыва</p>
                </div>

                <p class="response-message"><?= Html::encode($response->comment) ?></p>
            </div>

            <div class="feedback-wrapper">
                <p class="info-text">
                    <span class="current-time">
                        <?= Yii::$app->formatter->asRelativeTime($response->created_at); ?>
                    </span>
                </p>

                <p class="price price--small"><?= Html::encode($response->price); ?> ₽</p>
            </div>

            <?php if (Yii::$app->user->id === $task->author_id && $task->isStatusNew() && $response->isStatusPending()): ?>
                <div class="button-popup">
                    <a href="<?= Url::to(['tasks/accept-response', 'id' => $response->id]); ?>"
                       class="button button--blue button--small">Принять</a>
                    <a href="<?= Url::to(['tasks/reject-response', 'id' => $response->id]); ?>"
                       class="button button--orange button--small">Отказать</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>


    <!-- ТУТ СТАРЫЕ
    <div class="response-card">
        <img class="customer-photo" src="/img/man-glasses.png" width="146" height="156" alt="Фото заказчиков">
        <div class="feedback-wrapper">
            <a href="#" class="link link--block link--big">Астахов Павел</a>
            <div class="response-wrapper">
                <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
                <p class="reviews">2 отзыва</p>
            </div>
            <p class="response-message">
                Могу сделать всё в лучшем виде. У меня есть необходимый опыт и инструменты.
            </p>

        </div>
        <div class="feedback-wrapper">
            <p class="info-text"><span class="current-time">25 минут </span>назад</p>
            <p class="price price--small">3700 ₽</p>
        </div>
        <div class="button-popup">
            <a href="#" class="button button--blue button--small">Принять</a>
            <a href="#" class="button button--orange button--small">Отказать</a>
        </div>
    </div>
    -->

</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><?= Html::encode($task->category->name ?? ''); ?></dd>
            <dt>Дата публикации</dt>
            <dd><?= $task->getCreatedAtFormatted(); ?></dd>
            <dt>Срок выполнения</dt>
            <dd><?= $task->dueDateFormatted; ?></dd>
            <dt>Статус</dt>
            <dd><?= Html::encode($task->displayStatus()) ?></dd>
        </dl>
    </div>
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">my_picture.jpg</a>
                <p class="file-size">356 Кб</p>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">information.docx</a>
                <p class="file-size">12 Кб</p>
            </li>
        </ul>
    </div>
</div>
