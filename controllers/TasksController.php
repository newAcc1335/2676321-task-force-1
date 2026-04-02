<?php

namespace app\controllers;

use app\models\Categories;
use app\models\TasksForm;
use Yii;
use yii\web\Controller;
use app\models\Tasks;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $form = new TasksForm();
        $categories = Categories::find()->all();
        $tasks = Tasks::find()->where(['status' => Tasks::STATUS_NEW]);

        if ($form->load(Yii::$app->request->get())) {
            if (!empty($form->categories)) {
                $tasks->andWhere(['category_id' => $form->categories]);
            }

            if ($form->isWithoutExecutor) {
                $tasks->andWhere(['executor_id' => null]);
            }

            if ($form->period !== '') {
                $hours = (int)$form->period;

                $tasks->andWhere([
                        '>=',
                        'created_at',
                        date('Y-m-d H:i:s', time() - $hours * 3600)
                ]);
            }
        }

        $tasks = $tasks->with('category')->all();

        return $this->render('index', ['tasks' => $tasks, 'form' => $form, 'categories' => $categories]);
    }
}