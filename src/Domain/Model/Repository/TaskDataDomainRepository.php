<?php

namespace App\Domain\Model\Repository;

use App\Domain\Event\TaskUpdated;
use App\Domain\Model\Task;

class TaskDataDomainRepository implements TaskDataRepository
{

    /** @var Database */
    private $adapter;

    /** @var Table */
    private $table;


    /** @var EventDispatcher */
    private $eventDispatcher;


    public function __construct(
        Database $adapter,
        EventDispatcher $eventDispatcher
    ) {
        $this->adapter         = $adapter;
        $this->table           = new Table('tasks');
        $this->eventDispatcher = $eventDispatcher;
    }

    public function findById(TaskId $id): ?Task
    {
        /** @var Database\ResultRow|null $result */
        $result = $this->adapter->selectOne(
            $this->table,
            Database\Columns::withColumns(['status', 'description']),
            new Database\Where(['id' => $id->toString()])
        );

        if ($result === null) {
            return null;
        }

        return new Task(
            $id,
            $result->column('status'),
            $result->column('description')
        );
    }


    public function persist(Task $task): void
    {
        $dbData = [
            'id'          => $task->id()->toString(),
            'name'        => $task->status(),
            'company'     => $task->description(),
        ];

        $upsertResult = $this->adapter->upsert($this->table, new Data($dbData), new Database\Where([
            'id' => $task->id()->toString(),
        ]), 'created_at', 'updated_at');


        $this->eventDispatcher->dispatch(new TaskUpdated($task, new DateTimeImmutable()));
    }

}
