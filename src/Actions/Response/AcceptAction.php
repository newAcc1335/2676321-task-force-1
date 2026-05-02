<?php

namespace app\src\Actions\Response;

use app\models\Responses;

/**
 * Действие принятие отклика.
 *
 * Доступно автору нового задания для откликов в статусе "pending".
 */
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
