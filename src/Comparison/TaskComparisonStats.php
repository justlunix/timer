<?php

namespace Lunix\Timer\Comparison;

use Lunix\Timer\Data\TaskData;

class TaskComparisonStats
{
    public function __construct(
        public readonly mixed     $value,
        public readonly ?TaskData $taskData = null,
    )
    {
    }
}