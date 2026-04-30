<?php

namespace app\models;

use yii\base\Model;

class CompleteTaskForm extends Model
{
    public $comment;
    public $rating;

    public function rules(): array
    {
        return [
            [['rating', 'comment'], 'required'],
            [['comment'], 'string'],
            [['rating'], 'integer', 'min' => 1, 'max' => 5],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'comment' => 'Комментарий',
            'rating' => 'Оценка',
        ];
    }
}
