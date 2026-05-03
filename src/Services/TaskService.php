<?php

namespace app\src\Services;

use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Throwable;
use RuntimeException;
use app\models\Responses;
use app\models\Reviews;
use app\models\Tasks;
use app\models\AddTaskForm;
use app\models\TaskFiles;
use app\models\Cities;

/**
 * Сервис управления заданиями.
 *
 * Содержит бизнес-логику основных действий над заданиями.
 */
class TaskService
{
    /** Директория для хранения файлов */
    private const string FILES_PATH = '/files/';

    /**
     * Создаёт новое задание.
     *
     * @param AddTaskForm $form форма добавления задания (после валидации)
     * @param int $authorId ID заказчика
     * @return Tasks сохранённый объект задания
     * @throws RuntimeException если задание или файлы не удалось сохранить
     * @throws Throwable любое другое исключение
     */
    public function createTask(AddTaskForm $form, int $authorId): Tasks
    {
        $task = new Tasks();
        $task->title = $form->title;
        $task->description = $form->description;
        $task->category_id = $form->category_id;
        $task->location_name = $form->location_name;
        $task->budget = $form->budget;
        $task->due_date = $form->due_date;
        $task->author_id = $authorId;
        $task->status = Tasks::STATUS_NEW;
        $task->created_at = date('Y-m-d H:i:s');

        if (!empty($form->location_name)) {
            $coordinates = new GeocoderService()->search($form->location_name);

            if ($coordinates !== null) {
                $task->location = new Expression(
                    sprintf("ST_GeomFromText('POINT(%f %f)')", $coordinates['lng'], $coordinates['lat'])
                );

                if (!empty($coordinates['city'])) {
                    $city = Cities::findOne(['name' => $coordinates['city']]);

                    if ($city) {
                        $task->city_id = $city->id;
                    }
                }
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$task->save()) {
                Yii::error($task->getErrors(), 'TASK_SAVE_ERROR');
                throw new RuntimeException('Ошибка сохранения задания');
            }

            $this->saveFiles($task->id, $form->files);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $task;
    }

    /**
     * Добавляет отклик исполнителя на задание.
     *
     * @param int $taskId ID задания
     * @param int $userId ID исполнителя
     * @param string|null $comment комментарий к отклику
     * @param int|null $price предлагаемая цена
     * @throws NotFoundHttpException если задание не найдено
     * @throws RuntimeException если не удалось сохранить
     * @throws Exception
     */
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

    /**
     * Завершает задание и сохраняет отзыв об исполнителе.
     *
     * @param int $taskId ID задания
     * @param int $userId ID заказчика
     * @param string|null $comment отзыв к совершенной работе
     * @param int $rating оценка (целое число от 1 до 5)
     * @throws NotFoundHttpException если задание не найдено
     * @throws ForbiddenHttpException если нет доступа к действию
     * @throws RuntimeException если не удалось сохранить
     * @throws Throwable любое другое исключение
     */
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

    /**
     * Отказывается от задания (исполнитель), переводит в статус «провалено».
     *
     * @param int $taskId ID задания
     * @param int $userId ID исполнителя
     * @throws NotFoundHttpException если задание не найдено
     * @throws ForbiddenHttpException если нет прав для выполнения данного действия
     * @throws RuntimeException если задание не удалось сохранить
     * @throws Throwable любое другое исключение
     */

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

    /**
     * Отменяет задание (заказчик).
     * Применимо только для новых заданий (статус новое).
     *
     * @param int $taskId ID задания
     * @param int $userId ID заказчика
     * @throws NotFoundHttpException если задание не найдено
     * @throws ForbiddenHttpException если нет прав для данного действия
     * @throws RuntimeException если задание не удалось отменить
     * @throws Throwable любое другое исключение
     */
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

    /**
     * Сохраняет файлы на диск и в БД.
     *
     * @param int $taskId ID задания
     * @param array $files массив файлов
     * @throws RuntimeException если файл не удалось сохранить в БД
     * @throws Exception
     */
    private function saveFiles(int $taskId, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $fileDir = Yii::getAlias('@webroot') . self::FILES_PATH;

        if (!is_dir($fileDir)) {
            mkdir($fileDir, 0755, true);
        }

        foreach ($files as $file) {
            $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
            $file->saveAs($fileDir . $fileName);

            $taskFile = new TaskFiles();
            $taskFile->task_id = $taskId;
            $taskFile->file_path = self::FILES_PATH . $fileName;
            $taskFile->created_at = date('Y-m-d H:i:s');

            if (!$taskFile->save()) {
                Yii::error($taskFile->getErrors(), 'TASK_FILE_SAVE_ERROR');
                throw new RuntimeException('Ошибка сохранения файла ' . $fileName);
            }
        }
    }
}
