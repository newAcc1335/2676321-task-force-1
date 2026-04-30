<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Tasks $task */
?>

<section class="pop-up pop-up--fail pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отказ от задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отказаться от выполнения этого задания.<br>
            Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
        </p>

        <?= Html::a('Отказаться', Url::to([
            'tasks/run-task-action',
            'taskId' => $task->id,
            'actionCode' => 'fail'
        ]), ['class' => 'button button--pop-up button--orange']) ?>

        <div class="button-container">
            <?= Html::a('Закрыть окно', '#', ['class' => 'button--close']) ?>
        </div>
    </div>
</section>