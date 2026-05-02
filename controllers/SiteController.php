<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Контроллер для страницы ошибки.
 */
class SiteController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }
}
