<?php

namespace app\models;

use RuntimeException;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Форма настроек безопасности. Смена пароля\видимость контактов.
 *
 */
class SecurityForm extends Model
{
    public ?string $oldPassword = null;
    public ?string $newPassword = null;
    public ?string $newPasswordRepeat = null;
    public bool $is_customer_only = false;

    public function rules(): array
    {
        return [
            ['is_customer_only', 'boolean'],
            ['oldPassword', 'validateOldPassword', 'when' => fn () => !empty($this->oldPassword)],
            [
                'newPassword',
                'required',
                'when' => fn () => !empty($this->oldPassword),
                'message' => 'Введите новый пароль',
            ],
            [
                'newPassword',
                'string',
                'min' => 6,
                'tooShort' => 'Пароль должен содержать минимум 6 символов',
            ],
            [
                'newPasswordRepeat',
                'compare',
                'compareAttribute' => 'newPassword',
                'message' => 'Пароли не совпадают',
                'when' => fn () => !empty($this->oldPassword),
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'oldPassword'       => 'Старый пароль',
            'newPassword'       => 'Новый пароль',
            'newPasswordRepeat' => 'Повтор пароля',
            'is_customer_only'  => 'Показывать контакты только заказчику',
        ];
    }

    /**
     * Проверяет, что введён правильный пароль.
     */
    public function validateOldPassword(string $attribute): void
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->validatePassword($this->oldPassword)) {
            $this->addError($attribute, 'Неверный пароль');
        }
    }

    /**
     * Заполняет форму данными пользователя.
     */
    public function loadFromUser(Users $user): void
    {
        $this->is_customer_only = (bool) $user->is_customer_only;
    }

    /**
     * Обновляет данные пользователя.
     *
     * @throws Exception
     */
    public function update(Users $user): void
    {
        $user->is_customer_only = (int) $this->is_customer_only;

        if (!empty($this->oldPassword) && !empty($this->newPassword)) {
            $user->password_hash = Yii::$app->security->generatePasswordHash($this->newPassword);
        }

        if (!$user->save()) {
            throw new RuntimeException('Не удалось сохранить настройки');
        }
    }
}
