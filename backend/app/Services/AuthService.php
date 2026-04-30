<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\ApiTokenService;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly ApiTokenService $tokens,
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (! $user || $user['password'] !== $password) {
            throw new \RuntimeException('Invalid credentials');
        }

        $token = $this->tokens->issue([
            'id' => $user['id'],
            'name' => $user['name'],
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
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }
}
