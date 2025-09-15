<?php

namespace Schorpy\TriggerDev;


class EnvVars
{
    public static function list(string $projectRef, string $env): array
    {
        return TriggerDev::api('get', "projects/{$projectRef}/envvars/{$env}")->json();
    }

    public static function upload(string $projectRef, string $env, array $variables, bool $override = false): array
    {
        return TriggerDev::api('post', "projects/{$projectRef}/envvars/{$env}/import", [
            'variables' => $variables, // e.g. [ ['name' => 'SLACK_API_KEY', 'value' => 'slack_123'] ]
            'override'  => $override,
        ])->json();
    }

    public static function create(string $projectRef, string $env, string $name, string $value): array
    {
        return TriggerDev::api('post', "projects/{$projectRef}/envvars/{$env}", [
            'name'  => $name,
            'value' => $value,
        ])->json();
    }

    public static function retrieve(string $projectRef, string $env, string $name): array
    {
        return TriggerDev::api('get', "projects/{$projectRef}/envvars/{$env}/{$name}")->json();
    }

    public static function update(string $projectRef, string $env, string $name, string $value): array
    {
        return TriggerDev::api('put', "projects/{$projectRef}/envvars/{$env}/{$name}", [
            'value' => $value,
        ])->json();
    }

    public static function delete(string $projectRef, string $env, string $name): array
    {
        return TriggerDev::api('delete', "projects/{$projectRef}/envvars/{$env}/{$name}")->json();
    }
}
