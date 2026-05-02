<?php

namespace app\models;

use RuntimeException;
use Throwable;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Форма настройки аккаунта.
 *
 */
class EditProfileForm extends Model
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $birthday = null;
    public ?string $phone = null;
    public ?string $tg = null;
    public ?string $about = null;
    public ?UploadedFile $avatar = null;
    public $categories = [];
    public $city_id = null;

    public function rules(): array
    {
        return [
            [['name', 'email', 'city_id'], 'required', 'message' => 'Заполните данное поле'],
            [['name'], 'string', 'max' => 150],
            ['email', 'email', 'message' => 'Некорректный формат email'],
            [
                'email',
                'unique',
                'targetClass' => Users::class,
                'filter' => ['!=', 'id', Yii::$app->user->id],
                'message' => 'Этот email уже зарегистрирован',
            ],
            [
                'city_id',
                'exist',
                'targetClass' => Cities::class,
                'targetAttribute' => ['city_id' => 'id'],
                'message' => 'Выберите город из списка',
            ],
            ['birthday', 'date', 'format' => 'php:Y-m-d', 'message' => 'Указана некорректная дата'],
            ['phone', 'match', 'pattern' => '/^\d{11}$/', 'message' => 'Телефон должен состоять из 11 цифр'],
            ['tg', 'string', 'max' => 64],
            ['about', 'string'],
            [
                'categories',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => Categories::class,
                    'targetAttribute' => 'id',
                ],
            ],
            ['avatar', 'image', 'extensions' => ['jpg', 'jpeg', 'png'], 'maxSize' => 13 * 1024 * 1024],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Ваше имя',
            'email' => 'Email',
            'city_id' => 'Город',
            'about' => 'Информация о себе',
            'birthday' => 'День рождения',
            'categories' => 'Выбор специализаций',
            'avatar' => 'Аватар',
            'phone' => 'Номер телефона',
            'tg' => 'Telegram',
        ];
    }

    public function safeAttributes(): array
    {
        return ['name', 'email', 'about', 'birthday', 'phone', 'tg', 'city_id', 'categories'];
    }

    /**
     * Заполняет форму данными пользователя.
     */
    public function loadFromUser(Users $user): void
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->about = $user->about;
        $this->city_id = $user->city_id;
        $this->birthday = $user->birthday;
        $this->phone = $user->phone;
        $this->tg = $user->tg;
        $this->categories = array_map(fn ($c) => $c->id, $user->categories);
    }

    /**
     * Обновляет данные пользователя.
     *
     * @throws Throwable если не удалось сохранить
     */
    public function update(Users $user): void
    {
        $user->name = $this->name;
        $user->email = $this->email;
        $user->city_id = $this->city_id;
        $user->about = $this->about ?: null;
        $user->birthday = $this->birthday ?: null;
        $user->phone = $this->phone ?: null;
        $user->tg = $this->tg ?: null;

        if ($this->avatar) {
            $user->image_url = $this->saveAvatar();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$user->save()) {
                throw new RuntimeException('Не удалось сохранить пользователя');
            }

            $this->updateUserCategories($user);
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Сохраняет загруженный аватар на диск и возвращает относительный URL.
     */
    private function saveAvatar(): string
    {
        $fileDir = Yii::getAlias('@webroot') . '/files/';

        $fileName = uniqid() . '_' . $this->avatar->baseName . '.' . $this->avatar->extension;

        if (!$this->avatar->saveAs($fileDir . $fileName)) {
            throw new RuntimeException('Не удалось сохранить аватар');
        }

        return '/files/' . $fileName;
    }

    /**
     * Перезаписывает категории рабочие для данного пользователя.
     *
     */
    private function updateUserCategories(Users $user): void
    {
        $user->unlinkAll('categories', true);

        if (empty($this->categories)) {
            return;
        }

        $categories = Categories::findAll($this->categories);

        foreach ($categories as $category) {
            $user->link('categories', $category);
        }
    }
}
