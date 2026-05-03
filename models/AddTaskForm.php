<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Throwable;
use RuntimeException;
use app\validators\MinLengthValidator;
use app\src\Services\TaskService;

/**
 * Форма добавления нового задания.
 *
 * Выполняет валидацию входных данных, сохраняет задание и файлы к нему (использует TaskService::createTask())
 *
 */
class AddTaskForm extends Model
{
    public ?string $title = null;
    public ?string $description = null;
    public ?string $due_date = null;
    public ?string $location_name = null;
    public $category_id = null;
    public $budget = null;

    /** @var UploadedFile[] прикрепленные файлф */
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
            [
                'due_date',
                'compare',
                'compareValue' => date('Y-m-d'),
                'operator' => '>=',
                'message' => 'Дата не может быть раньше сегодня'
            ],
            ['files', 'file', 'skipOnEmpty' => true, 'maxFiles' => 13],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название задания',
            'description' => 'Описание задания',
            'category_id' => 'Категория',
            'budget' => 'Бюджет',
            'due_date' => 'Срок исполнения',
            'location_name' => 'Адрес',
            'files' => 'Файлы',
        ];
    }

    /**
     * Создаёт и сохраняет новое задание.
     *
     * @param int $authorId ID заказчика задания
     * @return Tasks сохранённый объект задания
     * @throws RuntimeException если не удалось сохранить
     * @throws Throwable
     */
    public function addTask(int $authorId): Tasks
    {
        return new TaskService()->createTask($this, $authorId);
    }
}
