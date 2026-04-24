<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
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
 *
 * @property Categories[] $categories
 * @property Cities $city
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Reviews[] $reviews0
 * @property Tasks[] $tasks
 * @property Tasks[] $tasks0
 * @property UserCategories[] $userCategories
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * ENUM field values
     */
    public const ROLE_AUTHOR = 'author';
    public const ROLE_EXECUTOR = 'executor';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['password_hash', 'birthday', 'phone', 'tg', 'image_url', 'city_id'], 'default', 'value' => null],
            [['is_customer_only'], 'default', 'value' => 0],
            [['created_at', 'birthday'], 'safe'],
            [['name', 'email', 'role'], 'required'],
            [['role'], 'string'],
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'name' => 'Name',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'role' => 'Role',
            'birthday' => 'Birthday',
            'phone' => 'Phone',
            'tg' => 'Tg',
            'image_url' => 'Image Url',
            'city_id' => 'City ID',
            'is_customer_only' => 'Is Customer Only',
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return ActiveQuery
     */
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Categories::class, ['id' => 'category_id'])->viaTable('user_categories', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Responses::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews0]].
     *
     * @return ActiveQuery
     */
    public function getReviews0(): ActiveQuery
    {
        return $this->hasMany(Reviews::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Tasks::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return ActiveQuery|TasksQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Tasks::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[UserCategories]].
     *
     * @return ActiveQuery|UserCategoriesQuery
     */
    public function getUserCategories()
    {
        return $this->hasMany(UserCategories::class, ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_AUTHOR => 'author',
            self::ROLE_EXECUTOR => 'executor',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleAuthor()
    {
        return $this->role === self::ROLE_AUTHOR;
    }

    public function setRoleToAuthor()
    {
        $this->role = self::ROLE_AUTHOR;
    }

    /**
     * @return bool
     */
    public function isRoleExecutor()
    {
        return $this->role === self::ROLE_EXECUTOR;
    }

    public function setRoleToExecutor()
    {
        $this->role = self::ROLE_EXECUTOR;
    }

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
     * @throws InvalidConfigException
     */
    public function getCreatedAtFormatted(): string
    {
        return Yii::$app->formatter->asDate($this->created_at, 'php:d F, H:i');
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
     * @param $id
     * @return Users|null
     */
    public static function findIdentity($id): ?self
    {
        return self::findOne($id);
    }

    /**
     * @param $token
     * @param $type
     * @return void
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return void
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * @param $authKey
     * @return void
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
