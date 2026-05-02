<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use app\src\Actions\RespondAction;
use app\src\Actions\CancelAction;
use app\src\Actions\CompleteAction;
use app\src\Actions\FailAction;
use app\src\Actions\Action;
use yii\db\Exception;

/**
 * Модель задания.
 *
 * @property int $id
 * @property string $created_at
 * @property string $title
 * @property string $description
 * @property int $category_id
 * @property int|null $city_id
 * @property string|null $location_name
 * @property string|null $location
 * @property int|null $budget
 * @property string|null $due_date
 * @property string $status
 * @property int $author_id
 * @property int|null $executor_id
 *
 * @property Categories $category
 * @property Cities $city
 * @property Responses[] $responses
 * @property TaskFiles[] $taskFiles
 */
class Tasks extends ActiveRecord
{
    public const string STATUS_NEW = 'new';
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_CANCELLED = 'cancelled';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_FAILED = 'failed';

    public static function tableName(): string
    {
        return 'tasks';
    }

    public function rules(): array
    {
        return [
            [['city_id', 'location_name', 'budget', 'due_date', 'executor_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['created_at', 'due_date'], 'safe'],
            [['title', 'description', 'category_id', 'author_id'], 'required'],
            [['description', 'status'], 'string'],
            ['location', 'safe'],
            [['category_id', 'city_id', 'budget', 'author_id', 'executor_id'], 'integer'],
            [['title', 'location_name'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    public function getCity(): ActiveQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Responses::class, ['task_id' => 'id']);
    }

    public function getTaskFiles(): ActiveQuery
    {
        return $this->hasMany(TaskFiles::class, ['task_id' => 'id']);
    }

    /**
     * Допустимые значения статуса задания.
     *
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

    public function displayStatus(): string
    {
        return self::optsStatus()[$this->status];
    }

    public function isStatusNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isStatusActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function setStatusToCancelled(): void
    {
        $this->status = self::STATUS_CANCELLED;
    }

    public function setStatusToCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
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

    /**
     * Возвращает массив доступных действий над данным заданием для данного пользователя.
     *
     * @param int $userId
     * @return Action[]
     */
    public function getAllowedActions(int $userId): array
    {
        $actions = [
            new RespondAction(),
            new CancelAction(),
            new CompleteAction(),
            new FailAction(),
        ];

        return array_values(array_filter(
            $actions,
            fn (Action $action) => $action->isAllowed($userId, $this)
        ));
    }

    /**
     * Проверяет, откликался ли данный пользователь на данное задание.
     * @param int $userId
     * @return bool
     */
    public function hasResponseFrom(int $userId): bool
    {
        return array_any($this->responses, fn ($response) => $response->executor_id === $userId);
    }

    /**
     * Возвращает отклики, которые может видеть данный пользователь.
     * (Заказчик видит все, исполнитель — только свой)
     *
     * @return Responses[]
     */
    public function getVisibleResponses(int $userId): array
    {
        if ($this->author_id === $userId) {
            return $this->responses;
        }

        return array_filter($this->responses, fn ($r) => $r->executor_id === $userId);
    }

    /**
     * Возвращает координаты из поля location БД.
     *
     * @return array{lat: float, lng: float}|null
     * @throws Exception
     */
    public function getCoordinates(): ?array
    {
        if (!$this->location) {
            return null;
        }

        $row = Yii::$app->db->createCommand(
            "SELECT ST_X(location) AS lng, ST_Y(location) AS lat FROM tasks WHERE id = :id",
            [':id' => $this->id]
        )->queryOne();

        return $row ? ['lat' => (float)$row['lat'], 'lng' => (float)$row['lng']] : null;
    }
}
