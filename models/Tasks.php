<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $created_at
 * @property string $title
 * @property string $description
 * @property int $category_id
 * @property int|null $city_id
 * @property string|null $location_name
 * @property string $location
 * @property int|null $budget
 * @property string|null $due_date
 * @property string $status
 * @property int $author_id
 * @property int|null $executor_id
 *
 * @property Users $author
 * @property Categories $category
 * @property Cities $city
 * @property Users $executor
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property TaskFiles[] $taskFiles
 */
class Tasks extends ActiveRecord
{
    /**
     * ENUM field values
     */
    public const string STATUS_NEW = 'new';
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_CANCELLED = 'cancelled';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_FAILED = 'failed';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['city_id', 'location_name', 'budget', 'due_date', 'executor_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['created_at', 'due_date'], 'safe'],
            [['title', 'description', 'category_id', 'location', 'author_id'], 'required'],
            [['description', 'location', 'status'], 'string'],
            [['category_id', 'city_id', 'budget', 'author_id', 'executor_id'], 'integer'],
            [['title', 'location_name'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'title' => 'Title',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'city_id' => 'City ID',
            'location_name' => 'Location Name',
            'location' => 'Location',
            'budget' => 'Budget',
            'due_date' => 'Due Date',
            'status' => 'Status',
            'author_id' => 'Author ID',
            'executor_id' => 'Executor ID',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery|UsersQuery
     */
    public function getAuthor(): ActiveQuery|UsersQuery
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery|CategoriesQuery
     */
    public function getCategory(): ActiveQuery|CategoriesQuery
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery|CitiesQuery
     */
    public function getCity(): ActiveQuery|CitiesQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return ActiveQuery|UsersQuery
     */
    public function getExecutor(): ActiveQuery|UsersQuery
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return ActiveQuery|ResponsesQuery
     */
    public function getResponses(): ActiveQuery|ResponsesQuery
    {
        return $this->hasMany(Responses::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery|ReviewsQuery
     */
    public function getReviews(): ActiveQuery|ReviewsQuery
    {
        return $this->hasMany(Reviews::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return ActiveQuery|TaskFilesQuery
     */
    public function getTaskFiles(): ActiveQuery|TaskFilesQuery
    {
        return $this->hasMany(TaskFiles::class, ['task_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TasksQuery the active query used by this AR class.
     */
    public static function find(): TasksQuery
    {
        return new TasksQuery(get_called_class());
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_ACTIVE => 'В работе',
            self::STATUS_CANCELLED => 'Отменено',
            self::STATUS_COMPLETED => 'Выполнено',
            self::STATUS_FAILED => 'Провалено',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus(): string
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function setStatusToNew(): void
    {
        $this->status = self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isStatusActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function setStatusToCancelled(): void
    {
        $this->status = self::STATUS_CANCELLED;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function setStatusToCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStatusFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function setStatusToFailed(): void
    {
        $this->status = self::STATUS_FAILED;
    }

    public function getCreatedAtFormatted(): ?string
    {
        return Yii::$app->formatter->asRelativeTime(strtotime($this->created_at));
    }

    /**
     * @throws InvalidConfigException
     */
    public function getDueDateFormatted(): string
    {
        return $this->due_date ? Yii::$app->formatter->asDatetime($this->due_date) : 'Не указан';
    }
}
