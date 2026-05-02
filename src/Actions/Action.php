<?php

namespace app\src\Actions;

use app\models\Tasks;

/**
 * Базовый класс для действий над заданием.
 *
 */
abstract class Action
{
    /** Текст на кнопке действия */
    abstract public function getName(): string;

    /** Код действия */
    abstract public function getActionCode(): string;

    /** Проверяет, хватает ли прав доступа для выполнения действия */
    abstract public function isAllowed(int $userId, Tasks $task): bool;

    /** CSS-класс для кнопки */
    abstract public function getButtonClass(): string;

    /** Возвращает название метода в TaskService, который выполняет данное действие */
    abstract public function getServiceMethod(): string;

    /**
     * URL для перехода по кнопке.
     * Может быть равен '#', если переход происходит через модальное окно.
     */
    abstract public function getHref(int $taskId): string;
}
