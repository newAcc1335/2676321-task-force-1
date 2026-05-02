<?php

namespace app\src\Actions;

use app\models\Tasks;

/**
 * Действие завершение задания.
 *
 * Доступно только автору задания, которое находится в статусе "Active".
 * Открывает модальное окно для отзыва и оценки.
 */
class CompleteAction extends Action
{
    public function getName(): string
    {
        return 'Завершить задание';
    }

    public function getActionCode(): string
    {
        return 'complete';
    }

    public function isAllowed(int $userId, Tasks $task): bool
    {
        return $task->author_id === $userId && $task->isStatusActive() && $task->executor_id !== null;
    }

    public function getButtonClass(): string
    {
        return 'button--pink';
    }

    public function getServiceMethod(): string
    {
        return 'completeTask';
    }

    public function getHref(int $taskId): string
    {
        return '#';
    }
}
