<?php

namespace app\models;

use yii\base\Model;

/**
 * Форма завершения задания с отзывом о проделанной работе.
 */
class CompleteTaskForm extends Model
{
    public ?string $comment = null;
    public ?int $rating = null;

    public function rules(): array
    {
        return [
            [['rating', 'comment'], 'required', 'message' => 'Заполните данное поле'],
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
