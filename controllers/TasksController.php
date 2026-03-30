<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Tasks;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $tasks = Tasks::find()
                ->with(['category'])
                ->where(['status' => Tasks::STATUS_NEW])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

        return $this->render('index', [
                'tasks' => $tasks,
        ]);
    }
}