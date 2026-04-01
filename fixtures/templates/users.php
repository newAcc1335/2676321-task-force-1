<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Users;
use app\models\Cities;

static $cityIds;
$cityIds = $cityIds ?? Cities::find()->select('id')->column();

return [
        'name' => $faker->name,
        'birthday' => $faker->date(),
        'email' => $faker->unique()->email,
        'password_hash' => Yii::$app->security->generatePasswordHash('password_' . $index),
        'image_url' => $faker->imageUrl(256, 256, 'people'),
        'phone' => '7' . $faker->numerify('##########'),
        'tg' => '@' . $faker->userName,
        'city_id' => $faker->randomElement($cityIds),
        'is_customer_only' => $faker->numberBetween(0, 1),
        'role' => $faker->randomElement([
                Users::ROLE_AUTHOR,
                Users::ROLE_EXECUTOR
        ]),
];