<?php

namespace app\models;

use yii\base\Model;

/**
 * Форма для фильтра списка заданий.
 */
class TasksForm extends Model
{
    public const array PERIOD_OPTIONS = [
        '' => 'За все время',
        '1' => '1 час',
        '12' => '12 часов',
        '24' => '24 часа',
    ];
    public array $categories = [];
    public bool $isWithoutResponses = false;
    public string $period = '';

    public function rules(): array
    {
        return [
            ['categories', 'each',
             'rule' => [
                 'exist',
                 'targetClass' => Categories::class,
                 'targetAttribute' => 'id'
             ]
            ],
            ['isWithoutResponses', 'boolean'],
            ['period', 'in', 'range' => array_keys(self::PERIOD_OPTIONS)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
                'categories' => 'Категории',
                'isWithoutResponses' => 'Без откликов',
                'period' => 'Период',
        ];
    }
}
