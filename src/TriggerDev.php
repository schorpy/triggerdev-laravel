<?php

namespace Schorpy\TriggerDev;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TriggerDev
{
    public const VERSION = '1.0.0';
    public const API = 'https://api.trigger.dev/api/v1/';
    /**
     * Indicates if migrations will be run.
     */
    public static bool $runsMigrations = true;

    /**
     * Indicates if routes will be registered.
     */
    public static bool $registersRoutes = true;


    public static function api(string $method, string $uri, array $payload = []): Response
    {
        $apiKey = config('triggerdev.secret_key');
        if (empty($apiKey)) {
            throw new Exception('TriggerDev Secret key not set.');
        }

        $method = strtolower($method);

        $response = Http::withToken($apiKey)
            ->withUserAgent('TriggerDev\Laravel/' . static::VERSION)
            ->accept('application/json')
            ->$method(static::API . ltrim($uri, '/'), $payload);

        if ($response->failed()) {
            $json = $response->json();
            $message = $json['error'] ?? "TriggerDev API request failed";
            throw new Exception($message);
        }

        return $response;
    }
}
