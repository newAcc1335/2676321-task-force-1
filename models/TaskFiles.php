<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Модель файла к заданию.
 *
 * @property int $id
 * @property string $created_at
 * @property int $task_id
 * @property string $file_path
 */
class TaskFiles extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'task_files';
    }

    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
            [['task_id', 'file_path'], 'required'],
            [['task_id'], 'integer'],
            [['file_path'], 'string', 'max' => 500],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }
}
