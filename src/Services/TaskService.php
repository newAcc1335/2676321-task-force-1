<?php

namespace app\src\Services;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Throwable;
use RuntimeException;
use app\models\Responses;
use app\models\Reviews;
use app\models\Tasks;

class TaskService
{
    public function respondTask(int $taskId, int $userId, ?string $comment, ?int $price): void
    {
        $task = Tasks::findOne($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Задача не найдена');
        }

        if ($task->status !== Tasks::STATUS_NEW) {
            throw new RuntimeException('Нельзя откликнуться на эту задачу');
        }

        if (Responses::find()->where(['task_id' => $taskId, 'executor_id' => $userId])->exists()) {
            throw new RuntimeException('Вы уже откликались');
        }

        $response = new Responses();
        $response->task_id = $taskId;
        $response->executor_id = $userId;
        $response->status = Responses::STATUS_PENDING;
        $response->comment = $comment;
        $response->price = $price;

        if (!$response->save()) {
            throw new RuntimeException('Ошибка создания отклика');
        }
    }

    public function completeTask(int $taskId, int $userId, ?string $comment, int $rating): void
    {

        $task = Tasks::findOne($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Задача не найдена');
        }

        if ($task->author_id !== $userId) {
            throw new ForbiddenHttpException('Нет прав для завершения задачи');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $task->setStatusToCompleted();

            if (!$task->save()) {
                throw new RuntimeException('Ошибка завершения задачи');
            }

            $review = new Reviews();
            $review->task_id = $taskId;
            $review->author_id = $userId;
            $review->executor_id = $task->executor_id;
            $review->comment = $comment;
            $review->rating = $rating;
            $review->created_at = date('Y-m-d H:i:s');

            if (!$review->save()) {
                throw new RuntimeException('Ошибка создания отзыва');
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function failTask(int $taskId, int $userId): void
    {
        $task = Tasks::findOne($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Задача не найдена');
        }

        if ($task->executor_id !== $userId) {
            throw new ForbiddenHttpException('Нет прав для отказа от задачи');
        }

        if ($task->status !== Tasks::STATUS_ACTIVE) {
            throw new RuntimeException('Нельзя отказаться от задачи в данном статусе');
        }

        $task->setStatusToFailed();

        if (!$task->save()) {
            throw new RuntimeException('Ошибка отказа');
        }
    }

    public function cancelTask(int $taskId, int $userId): void
    {
        $task = Tasks::findOne($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Задача не найдена');
        }

        if ($task->author_id !== $userId) {
            throw new ForbiddenHttpException('Нет прав для отмены задачи');
        }

        if (!$task->isStatusNew()) {
            throw new RuntimeException('Отменить можно только новое задание');
        }

        $task->setStatusToCancelled();

        if (!$task->save()) {
            throw new RuntimeException('Ошибка отмены задачи');
        }
    }
}
