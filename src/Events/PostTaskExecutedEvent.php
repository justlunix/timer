<?php

namespace Lunix\Timer\Events;

use Lunix\Timer\Data\TaskData;

class PostTaskExecutedEvent implements TimerEventInterface
{
    public function __construct(protected TaskData $taskData)
    {
    }

    public function getTaskData(): TaskData
    {
        return $this->taskData;
    }
}