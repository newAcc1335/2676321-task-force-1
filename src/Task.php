<?php

namespace App;

use app\Actions\Action;
use app\Actions\RespondAction;
use app\Actions\StartAction;
use app\Actions\CancelAction;
use app\Actions\CompleteAction;
use app\Actions\FailAction;

/**
 * Класс Задание.
 * Описывает задачу, ее статус и возможные действия
 */
class Task
{
    public const string STATUS_NEW = 'new';
    public const string STATUS_CANCELED = 'canceled';
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_FAILED = 'failed';
    private const array STATUSES = [
        self::STATUS_NEW => 'Новое',
        self::STATUS_ACTIVE => 'В работе',
        self::STATUS_COMPLETED => 'Выполнено',
        self::STATUS_CANCELED => 'Отменено',
        self::STATUS_FAILED => 'Провалено'
    ];
    private string $status {
        get {
            return $this->status;
        }
    }
    private int $authorId {
        get {
            return $this->authorId;
        }
    }
    private ?int $executorId {
        get {
            return $this->executorId;
        }
    }


    public function __construct(int $authorId, string $status = self::STATUS_NEW, ?int $executorId = null)
    {
        if (!isset(self::STATUSES[$status])) {
            throw new TaskException("Неизвестный статус: {$status}");
        }

        $this->status = $status;
        $this->authorId = $authorId;
        $this->executorId = $executorId;
    }

    /**
     * Возвращает список всех возможных статусов задачи.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    /**
     * Возвращает статус задачи после выполнения данного действия.
     *
     * @param Action $action
     * @return string|null Следующий статус или null, если действие недопустимо
     */
    public function getNextStatus(Action $action): ?string
    {
        $transitions = self::getTransitionsForStatus($this->status);
        $actionClass = get_class($action);

        return $transitions[$actionClass] ?? null;
    }

    /**
     * Возвращает список допустимых действий для данного пользователя.
     *
     * @param int $userId
     * @return array Список действий или пустой массив, если доступных действий нет
     */
    public function getAllowedActions(int $userId): array
    {
        $actions = self::getActionsForStatus($this->status);

        return array_values(array_filter(
            $actions,
            fn (Action $action) => $action->isAllowed($userId, $this->authorId, $this->executorId)
        ));
    }

    /**
     * Возвращает список возможных действий в зависимости от статуса задачи
     *
     * @param string $status Статус задачи
     * @return array
     */
    private static function getActionsForStatus(string $status): array
    {
        return match ($status) {
            self::STATUS_NEW => [
                new RespondAction(),
                new StartAction(),
                new CancelAction(),
            ],
            self::STATUS_ACTIVE => [
                new CompleteAction(),
                new FailAction(),
            ],
            default => [],
        };
    }

    /**
     * Возвращает таблицу переходов состояния задачи в зависимости от действия.
     *
     * @param string $status Статус задачи
     * @return array
     */
    private static function getTransitionsForStatus(string $status): array
    {
        return match ($status) {
            self::STATUS_NEW => [
                StartAction::class => self::STATUS_ACTIVE,
                CancelAction::class => self::STATUS_CANCELED,
            ],
            self::STATUS_ACTIVE => [
                CompleteAction::class => self::STATUS_COMPLETED,
                FailAction::class => self::STATUS_FAILED,
            ],
            default => [],
        };
    }
}
