<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use App\Support\ApiTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiToken
{
    public function __construct(private readonly ApiTokenService $tokenService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $payload = $this->tokenService->resolve($token);

        if (! $payload) {
            return ApiResponse::error('Unauthenticated.', 401, 'AUTH_TOKEN_INVALID');
        }

        $request->attributes->set('api_user', $payload['user']);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
