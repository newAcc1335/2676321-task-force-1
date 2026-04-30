<?php

namespace app\models;

use yii\base\Model;

class ResponseForm extends Model
{
    public $comment;
    public $price;

    public function rules(): array
    {
        return [
            [['price', 'comment'], 'default', 'value' => null],
            [['price'], 'integer', 'min' => 1],
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
