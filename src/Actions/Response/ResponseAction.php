<?php

namespace app\src\Actions\Response;

use app\models\Responses;

/**
 * Базовый класс для действий над откликом.
 *
 */
abstract class ResponseAction
{
    /** Текст на кнопке действия */
    abstract public function getName(): string;

    /** Код действия */
    abstract public function getActionCode(): string;

    /** CSS-класс для кнопки */
    abstract public function getButtonClass(): string;

    /** Проверяет, хватает ли прав доступа для выполнения действия */
    abstract public function isAllowed(int $userId, Responses $response): bool;
}
