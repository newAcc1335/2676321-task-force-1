<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\widgets\Alert;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$isRegistrationPage = Yii::$app->controller->route === 'registration/index';
$user = Yii::$app->user->identity;
$avatarUrl = $user && $user->isRoleExecutor()
        ? Url::to(['/users/view', 'id' => $user->id])
        : Url::to(['/edit-profile/index']);
$route = Yii::$app->controller->route;
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <?php if (!$isRegistrationPage): ?>
    <header class="page-header">
        <nav class="main-nav">
            <a href='<?= Url::to(['/tasks']); ?>' class="header-logo">
                <img class="logo-image" src="/img/logotype.png" width=227 height=60 alt="taskforce">
            </a>
            <div class="nav-wrapper">
                <ul class="nav-list">
                    <li class="list-item <?= str_starts_with($route, 'tasks/index') ? 'list-item--active' : ''; ?>">
                        <a href="<?= Url::to(['/tasks']); ?>" class="link link--nav" >Новое</a>
                    </li>
                    <li class="list-item <?= str_starts_with($route, 'my-tasks') ? 'list-item--active' : ''; ?>">
                        <a href="<?= Url::to(['/my-tasks']); ?>" class="link link--nav" >Мои задания</a>
                    </li>
                    <?php if ($user->isRoleAuthor()): ?>
                        <li class="list-item <?= str_starts_with($route, 'tasks/add') ? 'list-item--active' : ''; ?>">
                            <a href="<?= Url::to(['/tasks/add']); ?>" class="link link--nav" >Создать задание</a>
                        </li>
                    <?php endif; ?>
                    <li class="list-item <?= str_starts_with($route, 'edit-profile') ? 'list-item--active' : ''; ?>">
                        <a href="<?= Url::to(['/edit-profile']); ?>" class="link link--nav" >Настройки</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="user-block">
            <a href="<?= $avatarUrl; ?>">
                <img class="user-photo" src="<?= $user->image_url ?: '/img/man-glasses.png'; ?>" width="55" height="55" alt="Аватар">
            </a>
            <div class="user-menu">
                <p class="user-name"><?= Html::encode($user->name); ?></p>
                <div class="popup-head">
                    <ul class="popup-menu">
                        <li class="menu-item">
                            <a href="<?= Url::to(['/edit-profile']); ?>" class="link">Настройки</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="link">Связаться с нами</a>
                        </li>
                        <li class="menu-item">
                            <a href="<?= Url::to(['/landing/logout']); ?>" class="link">Выход из системы</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <main class="<?= $this->params['mainClass'] ?? 'container' ?>">
        <?= Alert::widget()?>
        <?= $content ?>
    </main>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
