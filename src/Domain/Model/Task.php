<?php


namespace App\Domain\Model;


class Task
{

    private $id;
    private $status;
    private $description;

    public function __construct(TaskId $id, ?string $status, ?string $description)
    {
        $this->id = $id;
        $this->status = $status;
        $this->description = $description;
    }

    public function updateStatus(Status $status): void
    {

    }


    public function updateDescription(string $description): void
    {

    }

}
