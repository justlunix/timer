<?php

namespace Lunix\Timer;

use Lunix\Timer\Data\TaskData;

class TaskResponse
{
    public string $cacheKey = '';
    public bool $cacheHit = false;
    public $result = null;

    public function __construct(
        private readonly TaskData $taskData
    )
    {
    }

    public function getTaskData(): TaskData
    {
        return $this->taskData;
    }
}