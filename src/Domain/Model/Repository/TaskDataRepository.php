<?php

namespace App\Domain\Model\Repository;

use App\Domain\Model\Task;

interface TaskDataRepository
{

    public function findById(TaskId $id) : ?Task;

    public function persist(Task $task) : void;

}
