<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\ApiTokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    private const RESET_PREFIX = 'techbyte:password-reset:';
    private const RESET_TTL_MINUTES = 60;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly ApiTokenService $tokens,
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (! $user || ! Hash::check($password, $user['password'])) {
            throw new \RuntimeException('Invalid credentials');
        }

        $name = $user['name'] ?? $user['username'] ?? null;
        $username = $user['username'] ?? $user['name'] ?? null;

        $token = $this->tokens->issue([
            'id' => $user['id'],
            'name' => $name,
            'username' => $username,
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return [
            'access_token' => $token['access_token'],
            'token_type' => $token['token_type'],
            'expires_in' => $token['expires_in'],
            'refresh_token' => null,
            'user' => [
                'id' => $user['id'],
                'name' => $name,
                'username' => $username,
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }

    public function register(string $username, string $email, string $password): array
    {
        if ($this->users->findByEmail($email)) {
            throw new \RuntimeException('EMAIL_TAKEN');
        }

        if ($this->users->findByUsername($username)) {
            throw new \RuntimeException('USERNAME_TAKEN');
        }

        $user = $this->users->create([
            'username' => $username,
            'email' => $email,
            // Let the User model handle hashing via the `password` cast
            'password' => $password,
            'role' => 'user',
        ]);

        $token = $this->tokens->issue([
            'id' => $user['id'],
            'name' => $user['name'] ?? $user['username'],
            'username' => $user['username'] ?? $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return [
            'access_token' => $token['access_token'],
            'token_type' => $token['token_type'],
            'expires_in' => $token['expires_in'],
            'refresh_token' => null,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'] ?? $user['username'],
                'username' => $user['username'] ?? $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }

    public function logout(?string $token): void
    {
        $this->tokens->revoke($token);
    }

    public function forgotPassword(string $email): ?string
    {
        $user = $this->users->findByEmail($email);

        if (! $user) {
            return null;
        }

        $token = Str::random(64);

        Cache::put(self::RESET_PREFIX . $token, [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'issued_at' => now()->toIso8601String(),
        ], now()->addMinutes(self::RESET_TTL_MINUTES));

        return $token;
    }
}
