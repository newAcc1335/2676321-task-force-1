<?php

namespace app\models;

use app\Actions\Response\AcceptAction;
use app\Actions\Response\RejectAction;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "responses".
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
    /**
     * ENUM field values
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'responses';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'task_id' => 'Task ID',
            'executor_id' => 'Executor ID',
            'price' => 'Price',
            'comment' => 'Comment',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

    /**
     * column status ENUM value labels
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

    /**
     * @return string
     */
    public function displayStatus(): string
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending(): void
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function setStatusToAccepted(): void
    {
        $this->status = self::STATUS_ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isStatusRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function setStatusToRejected(): void
    {
        $this->status = self::STATUS_REJECTED;
    }

    public function getAvailableActions(int $userId): array
    {
        $actions = [
            new AcceptAction(),
            new RejectAction(),
        ];

        return array_filter($actions, fn ($action) => $action->isAllowed($userId, $this));
    }
}
