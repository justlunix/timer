<?php

namespace Lunix\Timer;

use Ausi\SlugGenerator\SlugGenerator;
use DateTime;
use Lunix\Timer\Comparison\TaskComparison;
use Lunix\Timer\Data\TaskData;
use Lunix\Timer\Events\PostTaskExecutedEvent;
use Lunix\Timer\Events\PreTaskExecutedEvent;
use Lunix\Timer\Events\TimerEventInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class Timer
{
    public static bool $invalidateCaches = false;
    private static array $tasks = [];
    private static array $eventSubscriber = [];

    public static function task(string $name, callable $callback, ?float $expectedTime = null, ?DateTime $cacheUntil = null, ?TaskData $parent = null): TaskResponse
    {
        $taskData = self::$tasks[$name] = new TaskData(
            name: $name,
            startTime: microtime(true),
            expectedTime: $expectedTime,
            parent: $parent
        );

        $response = new TaskResponse(self::$tasks[$name]);

        $slugGenerator = new SlugGenerator();

        $cache = new FilesystemAdapter();
        $response->cacheKey = $slugGenerator->generate("timer:task-$name");
        $response->cacheUntil = $cacheUntil;

        self::throwEvent(new PreTaskExecutedEvent($taskData));

        self::$invalidateCaches && $cache->delete($response->cacheKey);
        if ($cache->hasItem($response->cacheKey)) {
            $response->cacheHit = true;
            $response->result = $cache->get($response->cacheKey, fn() => null);
        } else {
            if ($cacheUntil) {
                $response->result = $cache->get($response->cacheKey, function (ItemInterface $item) use ($callback, $cacheUntil, $taskData) {
                    $item->expiresAt($cacheUntil);
                    return $callback($taskData);
                });
            } else {
                $response->result = $callback($taskData);
            }
        }

        self::$tasks[$name]->endTime = microtime(true);

        self::throwEvent(new PostTaskExecutedEvent($taskData));

        $parent && $parent->addChild($response);

        return $response;
    }

    public static function compare(TaskData $taskDataA, TaskData $taskDataB): TaskComparison
    {
        return new TaskComparison($taskDataA, $taskDataB);
    }

    private static function throwEvent(TimerEventInterface $event): void
    {
        if (empty(self::$eventSubscriber[$event::class])) return;

        foreach (self::$eventSubscriber[$event::class] as $callable) {
            $callable($event);
        }
    }

    public static function subscribe(string $eventClass, callable $callback): void
    {
        self::$eventSubscriber[$eventClass] ??= [];
        self::$eventSubscriber[$eventClass][] = $callback;
    }
}