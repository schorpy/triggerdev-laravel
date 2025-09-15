<?php

namespace Schorpy\TriggerDev;


class Runs
{
    public static function list(): array
    {
        return TriggerDev::api('get', 'runs')->json();
    }

    public static function retrieve(string $runId): array
    {
        return TriggerDev::api('get', "runs/{$runId}")->json();
    }

    public static function replay(string $runId): array
    {
        return TriggerDev::api('post', "runs/{$runId}/replay")->json();
    }

    public static function cancel(string $runId): array
    {
        return TriggerDev::api('post', "runs/{$runId}/cancel")->json();
    }

    public static function reschedule(string $runId, string $delay): array
    {
        return TriggerDev::api('post', "runs/{$runId}/reschedule", [
            'delay' => $delay, // e.g. "1h" or ISO datetime
        ])->json();
    }

    public static function metadata(string $runId, array $metadata): array
    {
        return TriggerDev::api('put', "runs/{$runId}/metadata", [
            'metadata' => $metadata,
        ])->json();
    }
}
