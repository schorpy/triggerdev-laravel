# Laravel TriggerDev Package

A Laravel package to integrate **Trigger.Dev** APIs into your Laravel application, inspired by Laravel Cashier.  
It allows you to manage **Tasks, Runs, Schedules, and Environment Variables (EnvVars)** easily from your Laravel app.

---

## Features

- **Task Trigger**: Trigger individual tasks or batch tasks.
- **Runs**: List, retrieve, replay, cancel, reschedule, and update metadata for runs.
- **Schedules**: Create, update, activate, deactivate, and delete schedules.
- **EnvVars**: List, create, upload, update, retrieve, and delete environment variables.

---

# Usage
### Task Trigger

```php
use Schorpy\TriggerDev\Task;

// Trigger a single task
$response = Task::trigger('hello-world', ['foo' => 'bar'], ['priority' => 1]);

// Trigger a task with a public token
$response = Task::triggerWithPublicToken(
    taskId: 'hello-world',
    payload: ['foo' => 'bar'],
    options: ['priority' => 1],
    ttl: '1h'
);

// Batch trigger multiple tasks
$batchResponse = Task::batchTrigger([
    ['task' => 'task-1', 'payload' => ['a' => 1]],
    ['task' => 'task-2', 'payload' => ['b' => 2]],
]);
```
### Runs

```php
use Schorpy\TriggerDev\Runs;

// List all runs
$runs = Runs::list();

// Retrieve a single run
$run = Runs::retrieve('run_1234');

// Replay or cancel a run
$replayed = Runs::replay('run_1234');
$canceled  = Runs::cancel('run_1234');

// Reschedule a run
$rescheduled = Runs::reschedule('run_1234', '2025-09-16T10:00:00Z');

// Update metadata
$metadataUpdated = Runs::metadata('run_1234', [
    'key' => 'value',
    'env' => 'production'
]);
```
### Schedules
```php
use Schorpy\TriggerDev\Schedules;

// List schedules
$schedules = Schedules::list();

// Create a schedule
$newSchedule = Schedules::create('my-task', '0 0 * * *', 'external-id', 'America/New_York');

// Retrieve, update, or delete a schedule
$schedule = Schedules::retrieve('sch_1234');
$updated = Schedules::update('sch_1234', 'my-updated-task', '0 0 * * *', 'external-id', 'America/New_York');
Schedules::delete('sch_1234');

// Activate or deactivate a schedule
Schedules::activate('sch_1234');
Schedules::deactivate('sch_1234');
```
### EnvVars

```php
use Schorpy\TriggerDev\EnvVars;

$project = 'proj_yubjwjsfkxnylobaqvqz';
$env = 'dev';

// List environment variables
$vars = EnvVars::list($project, $env);

// Upload multiple variables
EnvVars::upload($project, $env, [
    ['name' => 'SLACK_API_KEY', 'value' => 'slack_123456'],
    ['name' => 'API_SECRET', 'value' => 'secret_789']
], false);

// Create a single variable
EnvVars::create($project, $env, 'SLACK_API_KEY', 'slack_123456');

// Retrieve, update, or delete a variable
$var = EnvVars::retrieve($project, $env, 'SLACK_API_KEY');
EnvVars::update($project, $env, 'SLACK_API_KEY', 'slack_new_987');
EnvVars::delete($project, $env, 'SLACK_API_KEY');
```

### Contributing

We welcome contributions! Here's how to help:

- Fork the repository to your GitHub account and clone it locally.
- Create a feature branch for your changes.
- Implement changes or add features.
- Test thoroughly to ensure functionality.
- Submit a pull request with a detailed description of your changes.
