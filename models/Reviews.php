<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель отзыва заказчика об исполнителе после завершения задания.
 *
 * @property int $id
 * @property string $created_at
 * @property int $task_id
 * @property int $author_id
 * @property int $executor_id
 * @property string $comment
 * @property int $rating
 *
 * @property Users $author
 * @property Tasks $task
 */
class Reviews extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'reviews';
    }

    public function rules(): array
    {
        return [
            [['task_id', 'author_id', 'executor_id', 'comment', 'rating', 'created_at'], 'required'],
            [['task_id', 'author_id', 'executor_id', 'rating'], 'integer'],
            [['comment'], 'string'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }
}
