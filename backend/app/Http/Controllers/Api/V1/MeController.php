<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MeService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __construct(private readonly MeService $me) {}

    public function me(Request $request)
    {
        $user = $request->attributes->get('api_user');

        if (! $user) {
            return ApiResponse::error('Unauthenticated.', 401, 'AUTH_UNAUTHENTICATED');
        }

        return ApiResponse::success($this->me->me($user), 'Current user fetched successfully.');
    }
}
