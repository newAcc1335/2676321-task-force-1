<?php

namespace app\src\Actions\Response;

use app\models\Responses;

class RejectAction extends ResponseAction
{
    public function getName(): string
    {
        return 'Отказать';
    }

    public function getActionCode(): string
    {
        return 'reject';
    }

    public function getButtonClass(): string
    {
        return 'button--orange';
    }

    public function isAllowed(int $userId, Responses $response): bool
    {
        $task = $response->task;

        return $task && $task->author_id === $userId && $task->isStatusNew() && $response->isStatusPending();
    }
}
