# Timer

A simple tool to track execution times of tasks.

### Install

Simply install the package via composer and you're good to go!

```bash
composer install justlunix/timer
```

### Usage

#### Simple tracking of a task

```php
$task = Timer::task('Fetching Customer Data From API', function() {
    // fetching...
});

echo $task->getTaskData()->getReadableTimeRun(); // => 2sec 15ms
```

#### Nested task tracking

```php
$task = Timer::task('Handling Web Request', function(TaskData $taskData) {
    $apiTask = Timer::task('Fetching Customer Data From API', function() {
        // fetching...
    }, parent: $taskData);
    $resTask = Timer::task('Building Response', function() use ($apiTask) {
        // building...
    }, parent: $taskData);
    
    return $resTask->result;
});
```

#### Subscribe to events

```php
Timer::subscribe(
    PostTaskExecutedEvent::class,
    function (TaskData $taskData) {
        // do something
    }
);
```

#### Compare tasks

```php
$issetTestTask = Timer::task('isset() performance', function () {
    // Implement performance test
});
$arraySearchTestTask = Timer::task('array_search() performance', function () {
    // Implement performance test
});

$taskComparison = Timer::compare($issetTestTask->getTaskData(), $arraySearchTestTask->getTaskData());
```

#### Cache task results

```php
// Invalidate caches via
Timer::$invalidateCaches = true;

$task = Timer::task('Fetching Customer Data From API', function() {
    // fetching...
}, cacheUntil: new \DateTime('tomorrow'));

$task = Timer::task('Fetching Customer Data From API', function() {
    // fetching...
}); // => Cache hit!
```

#### Export statistics

```php
// Export all tasks at the end with
Timer::exportAsFile(__DIR__ . '/timer.txt');

// TODO: or enable live logging into a file
Timer::enableLiveLogging(__DIR__ . '/timer.txt');

// TODO: or log to Spatie Ray!
Timer::enableRay();
```