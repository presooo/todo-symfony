<?php
declare(strict_types=1);

namespace App\Domain\Exception;


class TaskNotFound
{
    public function __construct(TaskId $id)
    {
        parent::__construct($id);
    }
}
