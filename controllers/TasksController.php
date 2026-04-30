<?php

namespace app\controllers;

use app\models\CompleteTaskForm;
use app\models\ResponseForm;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Categories;
use app\models\TasksForm;
use app\models\Tasks;
use app\models\AddTaskForm;
use app\models\Responses;

class TasksController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'add',
                    'respond-task',
                    'complete-task',
                    'accept-response',
                    'reject-response',
                    'run-task-action',
                ],
                'rules' => [
                    [
                        'actions' => ['add'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isRoleAuthor();
                        },
                    ],
                    [
                        'actions' => [
                            'respond-task',
                            'complete-task',
                            'accept-response',
                            'reject-response',
                            'run-task-action',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
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
        $task = Tasks::find()
            ->with(['responses.executor'])
            ->where(['id' => $id])
            ->one();

        if (!$task) {
            throw new NotFoundHttpException('Задание не найдено');
        }

        $responseForm = new ResponseForm();
        $completeForm = new CompleteTaskForm();

        return $this->render(
            'view',
            ['task' => $task, 'responseForm' => $responseForm, 'completeForm' => $completeForm]
        );
    }

    /**
     * @throws
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

    public function actionAcceptResponse(int $id): Response
    {
        $response = Responses::findOne($id);

        if (!$response) {
            throw new NotFoundHttpException('Отклик не найден');
        }

        $taskId = $response->task_id;

        try {
            Yii::$app->responseService->accept($id, Yii::$app->user->id);
            Yii::$app->session->setFlash('success', 'Отклик принят');
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->session->setFlash('danger', 'Не удалось принять отклик');
        }

        return $this->redirect(['tasks/view', 'id' => $taskId]);
    }

    public function actionRejectResponse(int $id): Response
    {
        $response = Responses::findOne($id);

        if (!$response) {
            throw new NotFoundHttpException('Отклик не найден');
        }

        $taskId = $response->task_id;

        try {
            Yii::$app->responseService->reject($id, Yii::$app->user->id);
            Yii::$app->session->setFlash('success', 'Отклик отклонён');
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->session->setFlash('danger', 'Не удалось отклонить отклик');
        }

        return $this->redirect(['tasks/view', 'id' => $taskId]);
    }

    public function actionRunTaskAction(int $taskId, string $actionCode): Response
    {
        $task = Tasks::findOne($taskId);

        if (!$task) {
            throw new NotFoundHttpException('Задача не найдена');
        }

        $actions = $task->getAllowedActions(Yii::$app->user->id);

        foreach ($actions as $action) {
            if ($action->getActionCode() === $actionCode) {

                $method = $action->getServiceMethod();

                Yii::$app->taskService->$method($taskId, Yii::$app->user->id);

                return $this->redirect(['tasks/view', 'id' => $taskId]);
            }
        }

        throw new ForbiddenHttpException('Действие недоступно');
    }

    public function actionRespondTask(int $id): Response|string
    {
        $task = Tasks::findOne($id);

        if (!$task) {
            throw new NotFoundHttpException('Задание не найдено');
        }

        $completeForm = new CompleteTaskForm();
        $responseForm = new ResponseForm();

        if ($responseForm->load(Yii::$app->request->post()) && $responseForm->validate()) {

            Yii::$app->taskService->respondTask($id, Yii::$app->user->id, $responseForm->comment, $responseForm->price);

            Yii::$app->session->setFlash('success', 'Отклик добавлен');

            return $this->redirect(['tasks/view', 'id' => $id]);
        }

        return $this->render('view', [
            'task' => $task,
            'responseForm' => $responseForm,
            'completeForm' => $completeForm,
        ]);
    }

    public function actionCompleteTask(int $id): Response|string
    {
        $task = Tasks::findOne($id);

        if (!$task) {
            throw new NotFoundHttpException('Задание не найдено');
        }

        $completeForm = new CompleteTaskForm();
        $responseForm = new ResponseForm();

        if ($completeForm->load(Yii::$app->request->post()) && $completeForm->validate()) {

            Yii::$app->taskService->completeTask($id, Yii::$app->user->id, $completeForm->comment, $completeForm->rating);
            Yii::$app->session->setFlash('success', 'Задание завершено');

            return $this->redirect(['tasks/view', 'id' => $id]);
        }

        return $this->render('view', [
            'task' => $task,
            'responseForm' => $responseForm,
            'completeForm' => $completeForm,
        ]);
    }
}
