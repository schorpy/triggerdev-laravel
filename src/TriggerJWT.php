<?php

namespace Schorpy\TriggerDev;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class TriggerJWT
{
    const BASE_URL      = "https://api.trigger.dev";
    const JWT_ALGORITHM = "HS256";
    const JWT_ISSUER    = "https://id.trigger.dev";
    const JWT_AUDIENCE  = "https://api.trigger.dev";

    /**
     * Generate JWT from claims (replicating Trigger.dev SDK approach)
     */
    public static function generateJWTFromClaims(string $runId, array $claims = [], string $ttl = "1h"): string
    {
        $now = time();

        // Base payload with standard JWT claims
        $payload = [
            "iss" => self::JWT_ISSUER,
            "aud" => self::JWT_AUDIENCE,
            "iat" => $now,
            "exp" => self::normalizeExpiration($ttl, $now),
        ];

        // Merge claims from Trigger.dev response headers
        $payload = array_merge($payload, $claims);

        // Add the run-specific scope (this is key!)
        $payload['scopes'] = ["read:runs:{$runId}"];

        $secretKey = config('triggerdev.secret_key');
        return JWT::encode($payload, $secretKey, self::JWT_ALGORITHM);
    }

    /**
     * Generate a generic JWT (updated to match SDK structure)
     */
    public function generateJWT(array $options): string
    {
        $secretKey = $options['secretKey'];
        $payload   = $options['payload'] ?? [];
        $now       = time();

        $finalClaims = [
            "iss" => self::JWT_ISSUER,
            "aud" => self::JWT_AUDIENCE,
            "iat" => $now,
            "exp" => $this->normalizeExpiration($options['expirationTime'] ?? "1h", $now),
        ];

        // Merge payload claims
        $finalClaims = array_merge($finalClaims, $payload);

        return JWT::encode($finalClaims, $secretKey, self::JWT_ALGORITHM);
    }

    /**
     * Create a Public Access Token scoped to a specific run (SDK-style)
     */
    public function makePublicTokenForRun(string $runId, string $taskName = null, string $ttl = "1h"): string
    {
        $secretKey = config('triggerdev.secret_key');
        return $this->generateJWT([
            "secretKey" => $secretKey,
            "payload" => [
                "scopes" => ["read:runs:{$runId}"], // This is the correct format from SDK
            ],
            "expirationTime" => $ttl,
        ]);
    }



    /**
     * Validate JWT
     */
    public function validateJWT(string $token, string $apiKey): array
    {
        try {
            $decoded = JWT::decode($token, new Key($apiKey, self::JWT_ALGORITHM));

            if ($decoded->iss !== self::JWT_ISSUER) {
                return ["ok" => false, "error" => "Invalid issuer"];
            }
            if ($decoded->aud !== self::JWT_AUDIENCE) {
                return ["ok" => false, "error" => "Invalid audience"];
            }

            return ["ok" => true, "payload" => (array) $decoded];
        } catch (ExpiredException $e) {
            return ["ok" => false, "error" => "Token expired"];
        } catch (SignatureInvalidException $e) {
            return ["ok" => false, "error" => "Invalid signature"];
        } catch (BeforeValidException $e) {
            return ["ok" => false, "error" => "Token not yet valid"];
        } catch (\Exception $e) {
            return ["ok" => false, "error" => $e->getMessage()];
        }
    }

    /**
     * Normalize expiration like "15m", "1h", "1d"
     */
    private static function normalizeExpiration($expirationTime, int $now): int
    {
        if (is_numeric($expirationTime)) {
            return (int) $expirationTime;
        }
        if ($expirationTime instanceof \DateTime) {
            return $expirationTime->getTimestamp();
        }
        if (is_string($expirationTime) && preg_match('/(\d+)(m|h|d)/', $expirationTime, $m)) {
            $value = (int) $m[1];
            return match ($m[2]) {
                "m" => $now + $value * 60,
                "h" => $now + $value * 3600,
                "d" => $now + $value * 86400,
                default => $now + 900,
            };
        }
        return $now + 900; // default 15m
    }
}
