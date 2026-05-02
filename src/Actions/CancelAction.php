<?php

namespace app\src\Actions;

use app\models\Tasks;
use yii\helpers\Url;

/**
 * Действие отмена задания.
 *
 * Доступно только автору задания, пока оно находится в статусе "New".
 */
class CancelAction extends Action
{
    public function getName(): string
    {
        return 'Отменить';
    }

    public function getActionCode(): string
    {
        return 'cancel';
    }

    public function isAllowed(int $userId, Tasks $task): bool
    {
        return $task->isStatusNew() && $task->author_id === $userId;
    }

    public function getButtonClass(): string
    {
        return 'button--orange';
    }

    public function getServiceMethod(): string
    {
        return 'cancelTask';
    }

    public function getHref(int $taskId): string
    {
        return Url::to([
            'tasks/run-task-action',
            'taskId' => $taskId,
            'actionCode' => $this->getActionCode()
        ]);
    }
}
