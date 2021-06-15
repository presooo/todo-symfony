<?php

namespace App\Domain\ReadModel\Repository;

use App\Domain\ReadModel\Tasks\TaskData;
use App\Domain\ReadModel\Tasks\TaskDataSet;
use App\Domain\ReadModel\Tasks\TaskFilter;

class TasksAPIRepository implements TasksRepository
{

    public function __construct(Database $adapter)
    {
        $this->adapter = $adapter;
    }


    public function tasks(UserId $userId, TaskFilter $filter): TaskDataSet
    {
        $filterSql = $this->extractFilterSql($filter);

        $query = sprintf(
            'SELECT t.* 
                FROM tasks t.
                    INNER JOIN users u ON t.user_id = u.id
                WHERE t.user_id = :userId
                %s
            ',
            $filterSql);

        $statement = $this->adapter->PDOInstance()->prepare($query);

        $statement->execute([
            'userId' => $userId->toString()
        ]);

        $rows = $statement->fetchAll();

        return $this->tasksFromQueryResult($rows);
    }


    public function tasksById(TaskId ...$ids) : TaskDataSet
    {
        // Very similar to the one above. They can potentially use the same code that is extracted in a separate function
        // that both this and the method above can use.
    }


    private function tasksFromQueryResult(array $rows) : TaskDataSet
    {
        $tasks = [];
        foreach ($rows as $row) {
            $tasks[] = new TaskData(
                TaskId::fromString($row['id']),
                $row['status'],
                $row['description']
            );
        }

        return new TaskDataSet(...$tasks);
    }
}
