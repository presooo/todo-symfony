<?php


namespace App\Domain\ReadModel\Tasks;


class TaskData
{

    private TaskId $id;
    private $status;
    private ?string $description;


    public function __construct(TaskId $id, $status, ?string $description)
    {
        $this->id = $id;
        $this->status = $status;
        $this->description = $description;
    }


    public function id(): TaskId
    {
        return $this->id;
    }


    // I think this ideally should be a Value object
    public function status()
    {
        return $this->status;
    }


    public function description(): ?string
    {
        return $this->description;
    }
}
