<?php

namespace Lunix\Timer;

use DateTime;
use Lunix\Timer\Data\TaskData;

class TaskResponse
{
    public bool $cacheHit = false;
    public string $cacheKey = '';
    public ?DateTime $cacheUntil = null;
    public mixed $result = null;

    public function __construct(
        private readonly TaskData $taskData
    )
    {
    }

    public function getTaskData(): TaskData
    {
        return $this->taskData;
    }

    public function toArray(): array
    {
        return [
            'cacheKey' => $this->cacheKey,
            'cacheHit' => $this->cacheHit,
            'cacheUntil' => $this->cacheUntil?->format('d.m.Y H:i:s'),
            'task' => $this->taskData->toArray()
        ];
    }
}