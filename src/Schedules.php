<?php

namespace Schorpy\TriggerDev;

class Schedules
{
    public static function list(): array
    {
        return TriggerDev::api('get', 'schedules')->json();
    }

    public static function create(string $task, string $cron, ?string $externalId = null, string $timezone = 'UTC'): array
    {
        return TriggerDev::api('post', 'schedules', [
            'task'        => $task,
            'cron'        => $cron,
            'externalId'  => $externalId,
            'timezone'    => $timezone,
        ])->json();
    }

    public static function retrieve(string $scheduleId): array
    {
        return TriggerDev::api('get', "schedules/{$scheduleId}")->json();
    }

    public static function update(
        string $scheduleId,
        string $task,
        string $cron,
        ?string $externalId = null,
        string $timezone = 'UTC'
    ): array {
        return TriggerDev::api('put', "schedules/{$scheduleId}", [
            'task'       => $task,
            'cron'       => $cron,
            'externalId' => $externalId,
            'timezone'   => $timezone,
        ])->json();
    }

    public static function delete(string $scheduleId): array
    {
        return TriggerDev::api('delete', "schedules/{$scheduleId}")->json();
    }

    public static function activate(string $scheduleId): array
    {
        return TriggerDev::api('post', "schedules/{$scheduleId}/activate")->json();
    }

    public static function deactivate(string $scheduleId): array
    {
        return TriggerDev::api('post', "schedules/{$scheduleId}/deactivate")->json();
    }

    public static function timezones(): array
    {
        return TriggerDev::api('get', 'timezones')->json();
    }
}
