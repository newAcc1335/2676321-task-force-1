<?php

namespace app\Actions;

use app\models\Tasks;

abstract class Action
{
    abstract public function getName(): string;
    abstract public function getActionCode(): string;
    abstract public function isAllowed(int $userId, Tasks $task): bool;
    abstract public function getButtonClass(): string;
    abstract public function getServiceMethod(): string;
    abstract public function getHref(int $taskId): string;
}
