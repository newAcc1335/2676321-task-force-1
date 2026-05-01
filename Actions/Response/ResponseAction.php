<?php

namespace app\Actions\Response;

use app\models\Responses;

abstract class ResponseAction
{
    abstract public function getName(): string;
    abstract public function getActionCode(): string;
    abstract public function getButtonClass(): string;
    abstract public function isAllowed(int $userId, Responses $response): bool;
}
