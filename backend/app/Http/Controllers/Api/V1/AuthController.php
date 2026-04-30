<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Services\AuthService;
use App\Support\ApiResponse;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function login(LoginRequest $request)
    {
        try {
            $payload = $this->auth->login(
                $request->validated('email'),
                $request->validated('password')
            );
        } catch (\RuntimeException) {
            return ApiResponse::error('Invalid credentials.', 401, 'AUTH_INVALID_CREDENTIALS');
        }

        return ApiResponse::success($payload, 'Login successful.');
    }
}
