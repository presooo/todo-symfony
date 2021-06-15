<?php

namespace App\Domain\ReadModel\Repository;

use App\Domain\ReadModel\Tasks\TaskDataSet;
use App\Domain\ReadModel\Tasks\TaskFilter;

interface TasksRepository
{
    public function tasks(UserId $userId, TaskFilter $filter): TaskDataSet;

    public function tasksById(TaskId ...$ids) : TaskDataSet;
}
