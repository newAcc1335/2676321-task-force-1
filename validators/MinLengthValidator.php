<?php

namespace app\validators;

use yii\validators\Validator;

/**
 * Валидатор минимальной длины текста без учёта пробелов
 */
class MinLengthValidator extends Validator
{
    /**
     * @var int минимальная длина текста без пробелов
     */
    public int $min = 0;

    /**
     * @var string сообщение об ошибке
     */
    public $message = 'Длина текста должна быть минимум {min} непробельных символов';

    /**
     * Валидирует атрибут модели
     *
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        $length = mb_strlen(preg_replace('/\s+/', '', (string)$model->$attribute));

        if ($length < $this->min) {
            $this->addError($model, $attribute, $this->message, ['min' => $this->min]);
        }
    }
}
