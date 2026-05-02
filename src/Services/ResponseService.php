<?php

namespace app\src\Services;

use RuntimeException;
use Yii;
use Throwable;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\Responses;

/**
 * Сервис управления откликами.
 *
 * Содержит бизнес-логику принятия и отклонения откликов.
 */
class ResponseService
{
    /**
     * Принимает отклик (назначает исполнителя и меняет статус).
     *
     * @param int $responseId ID отклика
     * @param int $userId ID заказчика
     * @throws NotFoundHttpException если отклик не найден
     * @throws ForbiddenHttpException если нет прав на действие
     * @throws RuntimeException если не удалось сохранить
     * @throws Throwable любое другое исключение
     */
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

    /**
     * Отклоняет отклик.
     *
     * @param int $responseId ID отклика
     * @param int $userId ID заказчика
     * @throws NotFoundHttpException если отклик не найден
     * @throws ForbiddenHttpException если нет прав на действие
     * @throws RuntimeException если не удалось сохранить
     * @throws Throwable любое другое исключение
     */
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
     * Находит отклик и проверяет, что у текущий пользователя достаточно прав (является автором задания).
     *
     * @throws ForbiddenHttpException если отклик не найден
     * @throws NotFoundHttpException если пользователь не является автором задания
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
