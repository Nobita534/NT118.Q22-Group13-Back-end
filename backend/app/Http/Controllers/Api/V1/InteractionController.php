<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\InteractionService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function __construct(private readonly InteractionService $interactions) {}

    public function like(int $id, Request $request)
    {
        $user = $request->attributes->get('api_user');
        $result = $this->interactions->like($id, $user);

        return ApiResponse::success($result, $result['already_liked'] ? 'Article already liked.' : 'Article liked successfully.');
    }

    public function bookmark(int $id, Request $request)
    {
        $user = $request->attributes->get('api_user');
        $result = $this->interactions->bookmark($id, $user);

        return ApiResponse::success($result, $result['already_bookmarked'] ? 'Article already bookmarked.' : 'Article bookmarked successfully.');
    }
}
