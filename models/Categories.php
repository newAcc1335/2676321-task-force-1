<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Модель категории задания
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 */
class Categories extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'categories';
    }

    public function rules(): array
    {
        return [
            [['name', 'icon'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['icon'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }
}
