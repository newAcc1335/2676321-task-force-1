<?php

namespace app\models;

use yii\base\Model;
use yii\db\Exception;

class AddTaskForm extends Model
{
    public string $title = '';
    public string $description = '';
    public int|null $category_id = null;
    public string $location_name = '';
    public int|null $budget = null;
    public string|null $due_date = null;

    public array $files = [];

    public function rules(): array
    {
        return [
            [['title', 'description', 'category_id'], 'required'],

            // 🔥 один универсальный валидатор
            [
                'title',
                'minNonSpaceLength',
                'params' => ['min' => 10],
                'message' => 'Минимум 10 непробельных символов'
            ],
            [
                'description',
                'minNonSpaceLength',
                'params' => ['min' => 30],
                'message' => 'Минимум 30 непробельных символов'
            ],

            [['description', 'location_name'], 'string'],
            [['category_id'], 'integer'],
            [['budget'], 'integer', 'min' => 1],

            [
                'category_id',
                'exist',
                'targetClass' => Categories::class,
                'targetAttribute' => 'id'
            ],

            ['due_date', 'safe'],

            [
                'files',
                'file',
                'skipOnEmpty' => true,
                'maxFiles' => 10
            ],
        ];
    }

    // 🔥 универсальный валидатор
    public function minNonSpaceLength($attribute, $params): void
    {
        $min = $params['min'] ?? 0;

        $length = mb_strlen(preg_replace('/\s+/', '', $this->$attribute));

        if ($length < $min) {
            $this->addError(
                $attribute,
                $params['message'] ?? "Минимум {$min} непробельных символов"
            );
        }
    }

    /**
     * @throws Exception
     */
    public function createTask(int $authorId): Tasks
    {
        $task = new Tasks();

        $task->title = $this->title;
        $task->description = $this->description;
        $task->category_id = $this->category_id;
        $task->location_name = $this->location_name;
        $task->budget = $this->budget;
        $task->due_date = $this->due_date;

        $task->author_id = $authorId;
        $task->status = Tasks::STATUS_NEW;
        $task->created_at = date('Y-m-d H:i:s');

        $task->save(false);

        return $task;
    }
}
