<?php

namespace app\models;

use yii\base\Model;

/**
 * Форма регистрации нового пользователя.
 */
class RegistrationForm extends Model
{
    public ?string $name = null;
    public ?string $email = null;
    public ?int $city_id = null;
    public ?string $password = null;
    public ?string $passwordRepeat = null;
    public bool $is_executor = false;

    public function rules(): array
    {
        return [
            [
                ['name', 'email', 'password', 'passwordRepeat', 'city_id'],
                'required',
                'message' => 'Это поле необходимо заполнить',
            ],
            ['email', 'email', 'message' => 'Некорректный формат email'],
            ['email', 'unique', 'targetClass' => Users::class, 'message' => 'Этот email уже зарегистрирован'],
            [
                'city_id',
                'exist',
                'targetClass' => Cities::class,
                'targetAttribute' => ['city_id' => 'id'],
                'message' => 'Выберете город из списка',
            ],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен содержать минимум 6 символов'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name'           => 'Имя',
            'email'          => 'Email',
            'city_id'        => 'Город',
            'password'       => 'Пароль',
            'passwordRepeat' => 'Повтор пароля',
            'is_executor'    => 'Я собираюсь откликаться на заказы',
        ];
    }
}
