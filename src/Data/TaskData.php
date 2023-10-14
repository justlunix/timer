<?php

namespace Lunix\Timer\Data;

use Carbon\CarbonInterval;

class TaskData
{
    public function __construct(
        protected string    $name,
        protected float     $startTime,
        public ?float       $expectedTime,
        protected ?TaskData $parent,
        public ?float       $endTime = null,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimeRun(): ?float
    {
        if ($this->endTime === null) {
            return null;
        }

        return $this->endTime - $this->startTime;
    }

    public function getParent(): ?TaskData
    {
        return $this->parent;
    }

    public function getReadableTimeRun(): string
    {
        return CarbonInterval::seconds($this->getTimeRun())->forHumans(['short' => true, 'minimumUnit' => 'ms']);
    }

    public function getReadableExpectedTime(): string
    {
        return CarbonInterval::seconds($this->expectedTime)->forHumans(['short' => true, 'minimumUnit' => 'ms']);
    }
}