<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\db\Expression;
use RuntimeException;
use app\src\Services\GeocoderService;
use app\validators\MinLengthValidator;

class AddTaskForm extends Model
{
    public $title;
    public $description;
    public $category_id;
    public $budget;
    public $due_date;
    public $location_name;

    public array $files = [];

    public function rules(): array
    {
        return [
            [['title', 'description', 'category_id'], 'required', 'message' => 'Заполните данное поле'],
            [['description', 'location_name'], 'string'],
            [['category_id'], 'integer'],
            [
                ['budget'],
                'integer',
                'min' => 1,
                'message' => 'Бюджет должен быть числом',
                'tooSmall' => 'Бюджет должен быть больше 0',
            ],
            [
                'category_id',
                'exist',
                'targetClass' => Categories::class,
                'targetAttribute' => 'id',
                'message' => 'Выберите категорию из списка',
            ],
            ['title', MinLengthValidator::class, 'min' => 10],
            ['description', MinLengthValidator::class, 'min' => 30],
            ['due_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Указана некорректная дата'],
            ['files', 'file', 'skipOnEmpty' => true, 'maxFiles' => 13],
        ];
    }

    /**
     * @throws Exception
     */
    public function addTask(int $authorId): Tasks
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

        if (!empty($this->location_name)) {
            $coordinates = new GeocoderService()->search($this->location_name);

            if ($coordinates !== null) {
                $task->location = new Expression(
                    sprintf("ST_GeomFromText('POINT(%f %f)')", $coordinates['lng'], $coordinates['lat'])
                );
            }
        }

        if (!$task->save()) {
            Yii::error($task->getErrors(), 'TASK_SAVE_ERROR');
            throw new RuntimeException('Ошибка сохранения задачи');
        }

        foreach ($this->files as $file) {
            $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
            $fileDir = Yii::getAlias('@webroot/files/');

            if (!is_dir($fileDir)) {
                mkdir($fileDir, 0755, true);
            }

            $file->saveAs($fileDir . $fileName);

            $taskFile = new TaskFiles();
            $taskFile->task_id = $task->id;
            $taskFile->file_path = '/files/' . $fileName;
            $taskFile->created_at = date('Y-m-d H:i:s');
            $taskFile->save();
        }

        return $task;
    }
}
