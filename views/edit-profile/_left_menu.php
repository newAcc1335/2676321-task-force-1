<?php
/** @var string $activeTab */

use yii\helpers\Url;

?>

<div class="left-menu left-menu--edit">
    <h3 class="head-main head-task">Настройки</h3>
    <ul class="side-menu-list">
        <li class="side-menu-item <?= $activeTab === 'profile' ? 'side-menu-item--active' : '' ?>">
            <a href="<?= Url::to(['/edit-profile']) ?>" class="link link--nav">Мой профиль</a>
        </li>
        <li class="side-menu-item <?= $activeTab === 'security' ? 'side-menu-item--active' : '' ?>">
            <a href="<?= Url::to(['/edit-profile/security']) ?>" class="link link--nav">Безопасность</a>
        </li>
    </ul>
</div>