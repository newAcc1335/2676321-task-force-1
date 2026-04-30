<?php

/** @var Tasks $task */
/** @var ResponseForm $responseForm */
/** @var CompleteTaskForm $completeForm */

use app\models\CompleteTaskForm;
use app\models\ResponseForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Tasks;

$this->params['mainClass'] = 'main-content container';
$this->title = 'Task №' . $task->id;

$userId = Yii::$app->user->id;
$isAuthor = $userId === $task->author_id;
$taskActions = $task->getAllowedActions($userId);
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

    <div class="task-map">
        <img class="map" src="/img/map.png" width="725"
             height="346" alt="<?= Html::encode($task->location_name ?? ''); ?>">
        <p class="map-address town"><?= Html::encode($task->city->name ?? ''); ?></p>
        <p class="map-address"><?= Html::encode($task->location_name ?? ''); ?></p>
    </div>

    <?php if ($isAuthor || $task->hasResponseFrom($userId)): ?>
        <h4 class="head-regular">Отклики на задание</h4>

        <?php foreach ($task->responses as $response): ?>
            <?php if (!$isAuthor && $response->executor_id !== $userId) {
                continue;
            }
            $responseActions = $response->getAvailableActions($userId); ?>

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

                <?php if (!empty($responseActions)): ?>
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
                <?php endif; ?>
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

<?= $this->render('_response_modal', ['task' => $task, 'responseForm' => $responseForm]); ?>
<?= $this->render('_complete_modal', ['task' => $task, 'completeForm' => $completeForm]); ?>
<?= $this->render('_fail_modal', ['task' => $task]); ?>
<div class="overlay <?= ($completeForm->hasErrors() || $responseForm->hasErrors()) ? 'db' : '' ?>"></div>
