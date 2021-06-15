<?php

namespace App\Domain\Event;

use App\Domain\Model\Task;

class TaskUpdated implements Event
{

    private Task $entity;
    private DateTimeImmutable $dateTime;

    public function __construct(Task $entity, DateTimeImmutable $updatedAt)
    {
        $this->entity   = $entity;
        $this->dateTime = $updatedAt;
    }

    public function call() : Task
    {
        return $this->entity;
    }

    public function updatedAt() : DateTimeImmutable
    {
        return $this->dateTime;
    }

    public static function name(): string
    {
        return __CLASS__;
    }
}
