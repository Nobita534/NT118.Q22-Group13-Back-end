<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

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

    public function register(RegisterRequest $request)
    {
        try {
            $payload = $this->auth->register(
                $request->validated('username'),
                $request->validated('email'),
                $request->validated('password')
            );
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'EMAIL_TAKEN') {
                return ApiResponse::error('Email already exists.', 409, 'AUTH_EMAIL_TAKEN');
            }

            if ($exception->getMessage() === 'USERNAME_TAKEN') {
                return ApiResponse::error('Username already exists.', 409, 'AUTH_USERNAME_TAKEN');
            }

            if (config('app.debug')) {
                return ApiResponse::error('Registration failed: ' . $exception->getMessage(), 400, 'AUTH_REGISTER_FAILED');
            }

            return ApiResponse::error('Registration failed.', 400, 'AUTH_REGISTER_FAILED');
        }

        return ApiResponse::success($payload, 'Register successful.', 201);
    }

    public function logout(Request $request)
    {
        $this->auth->logout($request->attributes->get('api_token'));

        return ApiResponse::success(null, 'Logout successful.');
    }

    // TODO: Enable forgot-password after the full reset flow is defined.
    // public function forgotPassword(ForgotPasswordRequest $request)
    // {
    //     $token = $this->auth->forgotPassword($request->validated('email'));
    //
    //     return ApiResponse::success(
    //         $token ? ['reset_token' => $token] : null,
    //         'Reset instructions sent.'
    //     );
    // }
}
