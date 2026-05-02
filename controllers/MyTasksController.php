<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Tasks;

/**
 * Страница «Мои задания».
 *
 * Показывает задания, в которых принимает участие текущий пользователь.
 * Фильтры зависят от роли пользователя.
 */
class MyTasksController extends Controller
{
    private const string FILTER_NEW     = 'new';
    private const string FILTER_ACTIVE  = 'active';
    private const string FILTER_EXPIRED = 'expired';
    private const string FILTER_CLOSED  = 'closed';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(?string $status = null): string
    {
        $user = Yii::$app->user->identity;

        if ($user->isRoleAuthor()) {
            $status = $status ?? self::FILTER_NEW;
            $tasks = $this->getAuthorTasks($user->id, $status);
            $statusFilters = $this->getAuthorStatusFilters($status);
        } else {
            $status = $status ?? self::FILTER_ACTIVE;
            $tasks = $this->getExecutorTasks($user->id, $status);
            $statusFilters = $this->getExecutorStatusFilters($status);
        }

        return $this->render('index', [
            'tasks' => $tasks,
            'statusFilters' => $statusFilters,
            'sectionTitle' => $this->getStatusLabel($status),
            'isAuthor' => $user->isRoleAuthor(),
        ]);
    }

    /**
     * Возвращает задания заказчика по фильтру статуса.
     *
     * @return Tasks[]
     */
    private function getAuthorTasks(int $userId, string $status): array
    {
        $authorTasks = Tasks::find()->with('category')->where(['author_id' => $userId]);

        return match ($status) {
            self::FILTER_NEW => $authorTasks->andWhere(['status' => Tasks::STATUS_NEW])->all(),
            self::FILTER_ACTIVE => $authorTasks->andWhere(['status' => Tasks::STATUS_ACTIVE])->all(),
            self::FILTER_CLOSED => $authorTasks->andWhere([
                'status' => [
                    Tasks::STATUS_CANCELLED,
                    Tasks::STATUS_COMPLETED,
                    Tasks::STATUS_FAILED,
                ]
            ])->all(),
            default => [],
        };
    }

    /**
     * Возвращает пункты бокового меню для заказчика.
     *
     * @param string $currentStatus
     * @return array
     */
    private function getAuthorStatusFilters(string $currentStatus): array
    {
        return [
            ['label' => 'Новые', 'status' => 'new', 'active' => $currentStatus === 'new'],
            ['label' => 'В процессе', 'status' => 'active', 'active' => $currentStatus === 'active'],
            ['label' => 'Закрытые', 'status' => 'closed', 'active' => $currentStatus === 'closed'],
        ];
    }

    /**
     * Возвращает задания исполнителя по фильтру статуса.
     *
     * @return Tasks[]
     */
    private function getExecutorTasks(int $userId, string $status): array
    {
        $executorTasks = Tasks::find()->with('category')->where(['executor_id' => $userId]);

        return match ($status) {
            self::FILTER_ACTIVE => $executorTasks->andWhere(['status' => Tasks::STATUS_ACTIVE])
                ->andWhere(['or', ['due_date' => null], ['>=', 'due_date', date('Y-m-d')]])
                ->all(),
            self::FILTER_EXPIRED => $executorTasks->andWhere(['status' => Tasks::STATUS_ACTIVE])
                ->andWhere(['<', 'due_date', date('Y-m-d')])
                ->all(),
            self::FILTER_CLOSED => $executorTasks->andWhere([
                'status' => [
                    Tasks::STATUS_COMPLETED,
                    Tasks::STATUS_FAILED,
                ]
            ])->all(),
            default => [],
        };
    }

    /**
     * Возвращает пункты бокового меню для исполнителя.
     *
     * @param string $currentStatus
     * @return array
     */
    private function getExecutorStatusFilters(string $currentStatus): array
    {
        return [
            ['label' => 'В процессе', 'status' => 'active', 'active' => $currentStatus === 'active'],
            ['label' => 'Просрочено', 'status' => 'expired', 'active' => $currentStatus === 'expired'],
            ['label' => 'Закрытые', 'status' => 'closed', 'active' => $currentStatus === 'closed'],
        ];
    }

    /**
     * Возвращает заголовок раздела для текущего статуса.
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            self::FILTER_NEW => 'Новые задания',
            self::FILTER_ACTIVE => 'Задания в процессе',
            self::FILTER_EXPIRED => 'Просроченные задания',
            self::FILTER_CLOSED => 'Закрытые задания',
            default => 'Задания',
        };
    }
}
