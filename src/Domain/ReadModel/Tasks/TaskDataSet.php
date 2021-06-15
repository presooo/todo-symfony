<?php


namespace App\Domain\ReadModel\Tasks;


class TaskDataSet
{

    /** @var TaskData[] $items*/
    private $items;


    public function __construct(TaskData ...$items)
    {
        $this->items = $items;
    }


    /**
     * @return TaskData[]
     */
    public function toArray() : array
    {
        return $this->items;
    }


    public function count() : int
    {
        return count($this->items);
    }


    public function toSerialisable() : array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = [
                'id'          => $item->id()->toString(),
                'status'      => $item->status(),
                'description' => $item->description()
            ];
        }

        return $items;
    }
}
