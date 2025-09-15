<?php

namespace Schorpy\TriggerDev;


class Task
{

    public static function trigger(string $taskIdentifier, array $payload = [], array $options = []): array
    {
        return TriggerDev::api('post', "tasks/{$taskIdentifier}/trigger", [
            'payload' => $payload,
            'options' => $options
        ])->json();
    }

    public static function batchTrigger(array $tasks): array
    {
        return TriggerDev::api('post', 'tasks/batch', [
            'tasks' => $tasks
        ])->json();
    }
    /**
     * Trigger task and get public token (complete SDK-style implementation)
     */
    public static function triggerWithPublicToken(
        string $taskId,
        array $payload,
        array $options = [],
        string $ttl = "1h"
    ): array {
        // Send API request
        $response = TriggerDev::api('post', "tasks/{$taskId}/trigger", [
            'payload' => $payload,
            'options' => $options,
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to trigger task: " . $response->body());
        }

        $responseData = $response->json();
        $runId = $responseData['id'] ?? null;

        if (!$runId) {
            throw new \Exception("Missing run ID in Trigger.dev response");
        }

        // Extract JWT from headers
        $jwtHeader = $response->header('x-trigger-jwt');
        if ($jwtHeader) {
            $publicToken = $jwtHeader;
        } else {
            $claimsHeader = $response->header('x-trigger-jwt-claims');
            $claims = $claimsHeader ? json_decode($claimsHeader, true) : [];

            $publicToken = TriggerJWT::generateJWTFromClaims($runId, $claims, $ttl);
        }

        return [
            'id' => $runId,
            'publicAccessToken' => $publicToken,
            'data' => $responseData,
        ];
    }
}
