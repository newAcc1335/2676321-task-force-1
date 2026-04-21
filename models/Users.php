<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;

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
class Users extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_AUTHOR = 'author';
    const ROLE_EXECUTOR = 'executor';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
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
     * @return \yii\db\ActiveQuery|CategoriesQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::class, ['id' => 'category_id'])->viaTable('user_categories', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery|CitiesQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery|ResponsesQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Responses::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery|ReviewsQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews0]].
     *
     * @return \yii\db\ActiveQuery|ReviewsQuery
     */
    public function getReviews0()
    {
        return $this->hasMany(Reviews::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery|TasksQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery|TasksQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Tasks::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[UserCategories]].
     *
     * @return \yii\db\ActiveQuery|UserCategoriesQuery
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
}
