<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'editor@techbyte.vn',
            'password' => 'Password@123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
                'meta' => ['request_id', 'timestamp'],
            ]);
    }
}
