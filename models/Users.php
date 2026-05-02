<?php

namespace app\models;

use DateMalformedStringException;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель пользователя сайта.
 *
 * @property int $id
 * @property string $created_at
 * @property string $name
 * @property string $email
 * @property string|null $password_hash
 * @property string $role
 * @property string|null $birthday
 * @property string|null $phone
 * @property string|null $tg
 * @property string|null $image_url
 * @property int|null $city_id
 * @property int $is_customer_only
 * @property string|null $about
 *
 * @property Categories[] $categories
 * @property Cities $city
 * @property Reviews[] $reviews0
 * @property Tasks[] $tasks0
 */
class Users extends ActiveRecord implements IdentityInterface
{
    public const string ROLE_AUTHOR = 'author';
    public const string ROLE_EXECUTOR = 'executor';

    public static function tableName(): string
    {
        return 'users';
    }

    public function rules(): array
    {
        return [
            [['password_hash', 'birthday', 'phone', 'tg', 'image_url', 'city_id'], 'default', 'value' => null],
            [['is_customer_only'], 'default', 'value' => 0],
            [['created_at', 'birthday'], 'safe'],
            [['name', 'email', 'role'], 'required'],
            [['role'], 'string'],
            [['about'], 'string'],
            [['city_id', 'is_customer_only'], 'integer'],
            [['name', 'email'], 'string', 'max' => 150],
            [['password_hash', 'image_url'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            [['tg'], 'string', 'max' => 64],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Categories::class, ['id' => 'category_id'])->viaTable('user_categories', ['user_id' => 'id']);
    }

    public function getCity(): ActiveQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Отзывы, оставленные как исполнителю.
     *
     * @return ActiveQuery
     */
    public function getReviews0(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['executor_id' => 'id']);
    }

    /**
     * Задания, где пользователь является исполнителем
     *
     * @return ActiveQuery
     */
    public function getTasks0(): ActiveQuery
    {
        return $this->hasMany(Tasks::class, ['executor_id' => 'id']);
    }

    /**
     * Отзывы об исполнителе с предзагруженными автором и заданием.
     *
     * Используется на странице профиля исполнителя.
     */
    public function getExecutorReviews(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['executor_id' => 'id'])->with(['author', 'task']);
    }

    /**
     * Возвращает возможные значения роли пользователя.
     *
     * @return string[]
     */
    public static function optsRole(): array
    {
        return [
            self::ROLE_AUTHOR => 'author',
            self::ROLE_EXECUTOR => 'executor',
        ];
    }

    public function isRoleAuthor(): bool
    {
        return $this->role === self::ROLE_AUTHOR;
    }

    public function isRoleExecutor(): bool
    {
        return $this->role === self::ROLE_EXECUTOR;
    }

    /**
     * Рейтинг исполнителя, считается по формуле:
     *      Рейтинг = сумма оценок / (кол-во отзывов + кол-во проваленных заданий).
     *
     * @return float искомый рейтинг
     */
    public function getRating(): float
    {
        $sum = $this->getReviews0()->sum('rating') ?? 0;
        $count = $this->getReviews0()->count();

        $failed = $this->getTasks0()
                ->andWhere(['status' => Tasks::STATUS_FAILED])
                ->count();

        $total = $count + $failed;

        return $total === 0 ? 0.0 : round($sum / $total, 2);
    }

    public function getCompletedTasksCount(): int
    {
        return $this->getTasks0()
                ->andWhere(['status' => Tasks::STATUS_COMPLETED])
                ->count();
    }

    public function getFailedTasksCount(): int
    {
        return $this->getTasks0()
                ->andWhere(['status' => Tasks::STATUS_FAILED])
                ->count();
    }

    /**
     * Место в рейтинге среди всех исполнителей.
     * Формула счета отличается от прошлой (не учитываем проваленные задания)
     *
     * @return int искомое место
     */
    public function getRank(): int
    {
        $avgRating = Reviews::find()->where(['executor_id' => $this->id])->average('rating') ?? 0;

        $rankPlace = Users::find()
            ->select(['users.id'])
            ->where(['users.role' => self::ROLE_EXECUTOR])
            ->joinWith('reviews0', false)
            ->groupBy('users.id')
            ->having('AVG(reviews.rating) > :avg', [':avg' => $avgRating])
            ->count();

        return (int)$rankPlace + 1;
    }

    /**
     * Строка с количеством отзывов (учитывает склонение).
     *
     * @return string
     */
    public function getReviewsText(): string
    {
        $countReviews = count($this->reviews0);

        return Yii::t(
            'app',
            '{n, plural, =0{нет отзывов} one{# отзыв} few{# отзыва} many{# отзывов} other{# отзывов}}',
            ['n' => $countReviews]
        );
    }

    public function getTgUsername(): ?string
    {
        return $this->tg ? ltrim($this->tg, '@') : null;
    }

    public function getTgUrl(): ?string
    {
        return $this->tg ? 'https://t.me/' . $this->tgUsername : null;
    }

    /**
     * Возвращает возраст пользователя
     *
     * @throws DateMalformedStringException
     */
    public function getAge(): ?int
    {
        if (!$this->birthday) {
            return null;
        }
        return new \DateTime($this->birthday)->diff(new \DateTime())->y;
    }

    /**
     * Возвращает форматированную дату создания задания.
     *
     * @throws InvalidConfigException
     */
    public function getCreatedAtFormatted(): string
    {
        return Yii::$app->formatter->asDate($this->created_at, 'php:d F, H:i');
    }

    /**
     * Проверяет, есть ли активное задание у исполнителя.
     *
     * @return bool
     */
    public function getIsActiveExecutor(): bool
    {
        return Tasks::find()->where(['executor_id' => $this->id, 'status' => Tasks::STATUS_ACTIVE])->exists();
    }

    /**
     * Проверяет, можно ли показывать контакты исполнителя просматривающему пользователю.
     *
     * @param int $viewerId
     * @return bool
     */
    public function isContactVisible(int $viewerId): bool
    {
        if (!$this->is_customer_only) {
            return true;
        }

        return Tasks::find()->where(['executor_id' => $this->id, 'author_id' => $viewerId])->exists();
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function findIdentity($id): ?self
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getId(): mixed
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }
}
