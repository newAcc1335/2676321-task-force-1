<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Tasks;
use app\models\Users;
use app\models\Categories;
use app\models\Cities;
use yii\db\Expression;

static $userIds;
static $categoryIds;
static $cityIds;

$cityIds = $cityIds ?? Cities::find()->select('id')->column();
$categoryIds = $categoryIds ?? Categories::find()->select('id')->column();
$userIds = $userIds ?? Users::find()->select('id')->column();

$lon = $faker->longitude;
$lat = $faker->latitude;

return [
        'title' => $faker->sentence(3),
        'description' => $faker->text(),
        'category_id' => $faker->randomElement($categoryIds),
        'city_id' => $faker->optional()->randomElement($cityIds),
        'location_name' => $faker->city . ', ' . $faker->streetName,
        'location' => new Expression("POINT($lon, $lat)"),
        'budget' => $faker->optional(0.8)->numberBetween(100, 77777),
        'due_date' => $faker->optional(0.3)->date('Y-m-d'),
        'author_id' => $faker->randomElement($userIds),
        'status' => $faker->randomElement([
                Tasks::STATUS_NEW,
                Tasks::STATUS_ACTIVE,
                Tasks::STATUS_CANCELLED,
                Tasks::STATUS_COMPLETED,
                Tasks::STATUS_FAILED,
        ]),
];