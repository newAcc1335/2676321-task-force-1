<?php

/** @var Users $user */
/** @var EditProfileForm $form */
/** @var Categories[] $categories */
/** @var Cities[] $cities */

use app\models\Categories;
use app\models\Cities;
use app\models\EditProfileForm;
use app\models\Users;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->params['mainClass'] = 'main-content main-content--left container';
$this->title = 'Настройки профиля';

$categoryOptions = ArrayHelper::map($categories, 'id', 'name');
$cityOptions = ArrayHelper::map($cities, 'id', 'name');
?>

<?= $this->render('_left_menu', ['activeTab' => 'profile']) ?>

<div class="my-profile-form">
    <?php
    $activeForm = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableClientValidation' => false,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'control-label'],
            'errorOptions' => ['class' => 'help-block', 'tag' => 'span'],
        ],
    ]); ?>

    <h3 class="head-main head-regular">Мой профиль</h3>

    <div class="photo-editing">
        <div>
            <p class="form-label">Аватар</p>
            <img class="avatar-preview"
                 src="<?= Html::encode($user->image_url ?: '/img/man-glasses.png'); ?>"
                 width="83" height="83" alt="Аватар">
        </div>
        <?= $activeForm->field($form, 'avatar', [
            'template' => '{input}{label}{error}',
        ])->fileInput(['hidden' => true, 'id' => 'avatar-input'])
            ->label('Сменить аватар', ['for' => 'avatar-input', 'class' => 'button button--black']); ?>
    </div>

    <?= $activeForm->field($form, 'name')->textInput(); ?>

    <div class="half-wrapper">
        <?= $activeForm->field($form, 'email')->input('email'); ?>
        <?= $activeForm->field($form, 'birthday')->input('date'); ?>
    </div>

    <div class="half-wrapper">
        <?= $activeForm->field($form, 'phone')->input('tel'); ?>
        <?= $activeForm->field($form, 'tg')->textInput(); ?>
    </div>

    <?= $activeForm->field($form, 'city_id')->dropDownList($cityOptions, ['prompt' => 'Выберите город']); ?>

    <?= $activeForm->field($form, 'about')->textarea(); ?>

    <?= $activeForm->field($form, 'categories', [
        'template' => "{label}\n<div class=\"checkbox-profile\">{input}</div>\n{error}",
    ])->checkboxList($categoryOptions, [
        'tag' => false,
        'itemOptions' => ['labelOptions' => ['class' => 'control-label']],
    ])->label('Выбор специализаций'); ?>

    <?= Html::submitButton('Сохранить', ['class' => 'button button--blue']); ?>

    <?php ActiveForm::end(); ?>
</div>