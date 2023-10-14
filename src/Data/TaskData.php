<?php

namespace Lunix\Timer\Data;

use Carbon\CarbonInterval;
use DateTime;
use Lunix\Timer\TaskResponse;

class TaskData
{
    private array $children = [];

    public function __construct(
        protected string    $name,
        protected float     $startTime,
        protected ?float    $expectedTime,
        protected ?TaskData $parent,
        public ?float       $endTime = null,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function getExpectedTime(): ?float
    {
        return $this->expectedTime;
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

    public function addChild(TaskResponse $child): void
    {
        $this->children[] = $child;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getReadableTimeRun(): string
    {
        return CarbonInterval::seconds($this->getTimeRun())->forHumans(['short' => true, 'minimumUnit' => 'ms']);
    }

    public function getReadableExpectedTime(): string
    {
        return CarbonInterval::seconds($this->expectedTime)->forHumans(['short' => true, 'minimumUnit' => 'ms']);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'expected' => [
                'value' => $this->expectedTime,
                'human' => $this->getReadableExpectedTime()
            ],
            'actual' => [
                'value' => $this->getTimeRun(),
                'human' => $this->getReadableTimeRun()
            ],
            'start' => [
                'value' => $this->startTime,
                'human' => DateTime::createFromFormat('U.u', $this->startTime)->format('d.m.Y H:i:s')
            ],
            'end' => [
                'value' => $this->endTime,
                'human' => DateTime::createFromFormat('U.u', $this->endTime)->format('d.m.Y H:i:s')
            ],
            'children' => array_map(fn(TaskResponse $taskData) => $taskData->toArray(), $this->getChildren()),
        ];
    }
}