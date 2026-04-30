<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ApiTokenService
{
    private const PREFIX = 'techbyte:api-token:';

    public function issue(array $user, int $ttlMinutes = 60): array
    {
        $token = Str::random(64);

        Cache::put($this->key($token), [
            'user' => $user,
            'issued_at' => now()->toIso8601String(),
            'expires_in' => $ttlMinutes * 60,
        ], now()->addMinutes($ttlMinutes));

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttlMinutes * 60,
        ];
    }

    public function resolve(?string $token): ?array
    {
        if (! $token) {
            return null;
        }

        return Cache::get($this->key($token));
    }

    public function revoke(?string $token): void
    {
        if (! $token) {
            return;
        }

        Cache::forget($this->key($token));
    }

    private function key(string $token): string
    {
        return self::PREFIX . $token;
    }
}
