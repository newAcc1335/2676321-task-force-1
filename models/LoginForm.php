<?php

namespace app\models;

use yii\base\Model;
use app\models\Users;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    private ?Users $_user = null;

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function getUser(): ?Users
    {
        if ($this->_user === null) {
            $this->_user = Users::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }

    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильный email или пароль');
            }
        }
    }

}
