<?php

namespace app\src\Actions;

use app\models\Tasks;
use app\models\Users;

/**
 * Действие отклика на задание.
 *
 * Доступно только исполнителю на новые задания, на которые он ещё не откликался.
 * Открывает модальное окно для отправки отклика.
 */
class RespondAction extends Action
{
    public function getName(): string
    {
        return 'Откликнуться на задание';
    }

    public function getActionCode(): string
    {
        return 'respond';
    }

    public function isAllowed(int $userId, Tasks $task): bool
    {
        $user = Users::findOne($userId);

        return $user?->isRoleExecutor()
            && $task->isStatusNew()
            && $task->executor_id === null
            && $task->author_id !== $userId
            && !$task->hasResponseFrom($userId);
    }

    public function getButtonClass(): string
    {
        return 'button--blue';
    }

    public function getServiceMethod(): string
    {
        return 'respondTask';
    }

    public function getHref(int $taskId): string
    {
        return '#';
    }
}
