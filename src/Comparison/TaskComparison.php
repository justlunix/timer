<?php

namespace Lunix\Timer\Comparison;

use Lunix\Timer\Data\TaskData;

class TaskComparison
{
    public function __construct(
        private TaskData $taskDataA,
        private TaskData $taskDataB,
    )
    {
    }

    public function executionTime(string $type): TaskComparisonStats
    {
        $obj = match ($type) {
            'min' => $this->taskDataA->getTimeRun() < $this->taskDataB->getTimeRun() ? $this->taskDataA : $this->taskDataB,
            'max' => $this->taskDataA->getTimeRun() > $this->taskDataB->getTimeRun() ? $this->taskDataA : $this->taskDataB,
        };
        return new TaskComparisonStats(
            $obj->getTimeRun(),
            $obj,
        );
    }

    public function closestToExpectation(): TaskComparisonStats
    {
        return new TaskComparisonStats(
            $this->taskDataA->getReadableTimeRun(),
            $this->taskDataA,
        );
    }

    public function farthestFromExpectation(): TaskComparisonStats
    {
        return new TaskComparisonStats(
            $this->taskDataA->getReadableTimeRun(),
            $this->taskDataA,
        );
    }
}