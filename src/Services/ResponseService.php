<?php

namespace app\src\Services;

use RuntimeException;
use Yii;
use Throwable;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\Responses;

class ResponseService
{
    public function accept(int $responseId, int $userId): void
    {
        $response = $this->findAccessibleResponse($responseId, $userId);

        $task = $response->task;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $response->setStatusToAccepted();

            if (!$response->save()) {
                throw new RuntimeException('Ошибка отклика');
            }

            $task->executor_id = $response->executor_id;
            $task->setStatusToActive();

            if (!$task->save()) {
                throw new RuntimeException('Ошибка задачи');
            }

            $transaction->commit();

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function reject(int $responseId, int $userId): void
    {
        $response = $this->findAccessibleResponse($responseId, $userId);
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $response->setStatusToRejected();

            if (!$response->save()) {
                throw new RuntimeException('Ошибка отклика');
            }

            $transaction->commit();

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    private function findAccessibleResponse(int $responseId, int $userId): Responses
    {
        $response = Responses::findOne($responseId);

        if (!$response) {
            throw new NotFoundHttpException('Отклик не найден');
        }

        if (!$response->task || $response->task->author_id !== $userId) {
            throw new ForbiddenHttpException('Нет прав для выполнения данного действия');
        }

        return $response;
    }
}
