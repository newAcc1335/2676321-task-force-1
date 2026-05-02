<?php

namespace app\src\Actions\Response;

use app\models\Responses;

class AcceptAction extends ResponseAction
{
    public function getName(): string
    {
        return 'Принять';
    }

    public function getActionCode(): string
    {
        return 'accept';
    }

    public function getButtonClass(): string
    {
        return 'button--blue';
    }

    public function isAllowed(int $userId, Responses $response): bool
    {
        $task = $response->task;

        return $task && $task->author_id === $userId && $task->isStatusNew() && $response->isStatusPending();
    }
}
