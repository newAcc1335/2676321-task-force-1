<?php

namespace app\src\Actions;

use app\models\Tasks;
use yii\helpers\Url;

class FailAction extends Action
{
    public function getName(): string
    {
        return 'Отказаться от задания';
    }

    public function getActionCode(): string
    {
        return 'fail';
    }

    public function isAllowed(int $userId, Tasks $task): bool
    {
        return $task->isStatusActive() && $task->executor_id === $userId;
    }

    public function getButtonClass(): string
    {
        return 'button--orange';
    }

    public function getServiceMethod(): string
    {
        return 'failTask';
    }

    public function getHref(int $taskId): string
    {
        return '#';
    }
}
