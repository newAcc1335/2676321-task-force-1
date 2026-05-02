<?php

namespace app\models;

use yii\base\Model;

/**
 * Форма отклика на задание.
 */
class ResponseForm extends Model
{
    public ?string $comment = null;
    public ?int $price = null;

    public function rules(): array
    {
        return [
            [['price', 'comment'], 'default', 'value' => null],
            [['price'], 'integer', 'min' => 1, 'tooSmall' => 'Цена должна быть больше 0'],
            [['comment'], 'string', 'max' => 1313],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'comment' => 'Комментарий',
            'price' => 'Стоимость',
        ];
    }
}
