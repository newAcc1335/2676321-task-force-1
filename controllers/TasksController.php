<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Categories;
use app\models\TasksForm;
use app\models\Tasks;
use app\models\AddTaskForm;

class TasksController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['add'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isRoleAuthor();
                        },
                    ],
                ],
            ],
        ];
    }

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

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $task = Tasks::findOne($id);

        if (!$task) {
            throw new NotFoundHttpException('Задание не найдено');
        }

        return $this->render('view', ['task' => $task]);
    }

    /**
     * @throws Exception
     */
    public function actionAdd(): Response|string
    {
        $form = new AddTaskForm();
        $categories = Categories::find()->all();

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $task = $form->addTask(Yii::$app->user->id);
                return $this->redirect(['/tasks/view', 'id' => $task->id]);
            }
        }

        return $this->render('add', ['addTaskForm' => $form, 'categories' => $categories]);
    }
}
