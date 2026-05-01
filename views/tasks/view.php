<?php

/** @var Tasks $task */
/** @var ResponseForm $responseForm */
/** @var CompleteTaskForm $completeForm */

use app\models\CompleteTaskForm;
use app\models\ResponseForm;
use app\widgets\StarsWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Tasks;
use yii\web\View;

$this->params['mainClass'] = 'main-content container';
$this->title = 'Task №' . $task->id;

$userId = Yii::$app->user->id;
$taskActions = $task->getAllowedActions($userId);
$visibleResponses = $task->getVisibleResponses($userId);

$this->registerJsFile(
    'https://api-maps.yandex.ru/2.1/?apikey=' . Yii::$app->params['yandexApiKey'] . '&lang=ru_RU',
    ['position' => View::POS_HEAD]
);
?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= Html::encode($task->title); ?></h3>

        <?php if ($task->budget): ?>
            <p class="price price--big"><?= $task->budget; ?> ₽</p>
        <?php endif; ?>
    </div>

    <p class="task-description"><?= Yii::$app->formatter->asNtext($task->description); ?></p>
    <?php foreach ($taskActions as $action): ?>
        <a href="<?= $action->getHref($task->id) ?>"
           class="button action-btn <?= $action->getButtonClass() ?>"
           data-action="<?= $action->getActionCode() ?>">
            <?= $action->getName() ?>
        </a>
    <?php endforeach; ?>

    <?php if ($task->location): ?>
        <?php $coords = $task->getCoordinates(); ?>
        <div class="task-map">
            <div id="map" class="map" style="width: 725px; height: 346px;"></div>
            <p class="map-address town"><?= Html::encode($task->city->name ?? '') ?></p>
            <p class="map-address"><?= Html::encode($task->location_name ?? '') ?></p>
        </div>

        <?php $this->registerJs("
            ymaps.ready(function () {
                var myMap = new ymaps.Map('map', {
                    center: [{$coords['lat']}, {$coords['lng']}],
                    zoom: 15
                });
                myMap.geoObjects.add(new ymaps.Placemark(
                    [{$coords['lat']}, {$coords['lng']}],
                    { balloonContent: '" . addslashes($task->location_name ?? '') . "' }
                ));
            });
        ") ?>
    <?php endif; ?>

    <?php if (!empty($visibleResponses)): ?>
        <h4 class="head-regular">Отклики на задание</h4>

        <?php foreach ($visibleResponses as $response): ?>
            <?php $responseActions = $response->getAvailableActions($userId); ?>

            <div class="response-card">
                <img class="customer-photo"
                     src="<?=Html::encode($response->executor->image_url ?? '/img/man-glasses.png'); ?>"
                     width="146" height="156" alt="Фото заказчиков">
                <div class="feedback-wrapper">
                    <a href="<?= Url::to(['/users/view', 'id' => $response->executor_id]); ?>"
                       class="link link--block link--big">
                        <?= Html::encode($response->executor->name ?? 'Безымянный'); ?>
                    </a>

                    <div class="response-wrapper">
                        <?= StarsWidget::widget(['rating' => $response->executor->rating, 'size' => 'small']); ?>
                        <p class="reviews">
                            <?= $response->executor->reviewsText; ?>
                        </p>
                    </div>

                    <p class="response-message"><?= Html::encode($response->comment) ?></p>
                </div>

                <div class="feedback-wrapper">
                    <p class="info-text">
                        <span class="current-time">
                            <?= Yii::$app->formatter->asRelativeTime($response->created_at); ?>
                        </span>
                    </p>

                    <?php if ($response->price): ?>
                        <p class="price price--small"><?= Html::encode($response->price); ?> ₽</p>
                    <?php endif; ?>
                </div>

                <div class="button-popup">
                    <?php foreach ($responseActions as $action): ?>
                        <a href="<?= Url::to([
                                'tasks/' . $action->getActionCode() . '-response',
                                'id' => $response->id
                        ]) ?>"
                           class="button <?= $action->getButtonClass(); ?> button--small">
                            <?= $action->getName(); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
        <?php if ($task->taskFiles): ?>
            <ul class="enumeration-list">
                <?php foreach ($task->taskFiles as $file): ?>
                    <li class="enumeration-item">
                        <a href="<?= Html::encode($file->file_path); ?>"
                           class="link link--block link--clip"
                           download>
                            <?= Html::encode(basename($file->file_path)) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?= $this->render('_response_modal', ['task' => $task, 'responseForm' => $responseForm]); ?>
<?= $this->render('_complete_modal', ['task' => $task, 'completeForm' => $completeForm]); ?>
<?= $this->render('_fail_modal', ['task' => $task]); ?>
<div class="overlay <?= ($completeForm->hasErrors() || $responseForm->hasErrors()) ? 'db' : ''; ?>"></div>
