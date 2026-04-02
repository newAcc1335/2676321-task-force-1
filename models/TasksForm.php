<?php

namespace app\models;

use yii\base\Model;

class TasksForm extends Model
{
    public array $categories = [];
    public bool $isWithoutExecutor = false;
    public string $period = '';
    public const array PERIOD_OPTIONS = [
            '' => 'За все время',
            '1' => '1 час',
            '12' => '12 часов',
            '24' => '24 часа',
    ];

    public function attributeLabels(): array
    {
        return [
                'categories' => 'Категории',
                'isWithoutExecutor' => 'Без исполнителя',
                'period' => 'Период',
        ];
    }

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
                ['isWithoutExecutor', 'boolean'],
                ['period', 'in', 'range' => array_keys(self::PERIOD_OPTIONS)],
        ];
    }
}