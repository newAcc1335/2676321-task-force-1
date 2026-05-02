<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Модель города для локации задания
 *
 * @property int $id
 * @property string $name
 * @property string $location
 */
class Cities extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'cities';
    }

    public function rules(): array
    {
        return [
            [['name', 'location'], 'required'],
            [['location'], 'string'],
            [['name'], 'string', 'max' => 150],
            [['name'], 'unique'],
        ];
    }
}
