<?php

namespace app\models;

use app\Actions\Response\AcceptAction;
use app\Actions\Response\RejectAction;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель отклика исполнителя на задание.
 *
 * @property int $id
 * @property string $created_at
 * @property int $task_id
 * @property int $executor_id
 * @property int|null $price
 * @property string|null $comment
 * @property string $status
 *
 * @property Users $executor
 * @property Tasks $task
 */
class Responses extends ActiveRecord
{
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_ACCEPTED = 'accepted';
    public const string STATUS_REJECTED = 'rejected';

    public static function tableName(): string
    {
        return 'responses';
    }

    public function rules(): array
    {
        return [
            [['price', 'comment'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['created_at'], 'safe'],
            [['task_id', 'executor_id'], 'required'],
            [['task_id', 'executor_id', 'price'], 'integer'],
            [['comment', 'status'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }

    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

    /**
     * Возвращает допустимые значения статуса.
     *
     * @return string[]
     */
    public static function optsStatus(): array
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_ACCEPTED => 'accepted',
            self::STATUS_REJECTED => 'rejected',
        ];
    }

    public function isStatusPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToAccepted(): void
    {
        $this->status = self::STATUS_ACCEPTED;
    }

    public function setStatusToRejected(): void
    {
        $this->status = self::STATUS_REJECTED;
    }

    /**
     * Возвращает массив с доступными действиями над откликом для данного пользователя.
     *
     * @param int $userId
     * @return array
     */
    public function getAvailableActions(int $userId): array
    {
        $actions = [
            new AcceptAction(),
            new RejectAction(),
        ];

        return array_filter($actions, fn ($action) => $action->isAllowed($userId, $this));
    }
}
