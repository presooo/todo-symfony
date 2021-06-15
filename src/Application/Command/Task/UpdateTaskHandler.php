<?php

namespace App\Application\Command\Task;

use App\Application\Command\CommandHandler;
use App\Domain\Model\Repository\TaskDataRepository;

class UpdateTaskHandler implements CommandHandler
{

    private $repository;

    public function __construct(TaskDataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(UpdateTask $command) : void
    {

        $task = $this->repository->findById(TaskId::fromString($command->id));

        $task->updateStatus($command->status);
        $task->updateDescription($command->description);

        $this->repository->persist($task);

    }
}
