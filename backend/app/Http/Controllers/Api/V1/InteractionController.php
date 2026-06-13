<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InteractionRequest;
use App\Services\InteractionService;
use App\Support\ApiResponse;

class InteractionController extends Controller
{
    protected InteractionService $interactionService;

    public function __construct(InteractionService $interactionService)
    {
        $this->interactionService = $interactionService;
    }

    public function like(InteractionRequest $request, $id)
    {
        // Lấy mảng thông tin User từ custom middleware của bạn
        $userArray = $request->user()->toArray();

        // dd($user);

        // Khớp đúng với signature mới ở tầng Service: (int $articleId, array $user)
        $result = $this->interactionService->toggleLike((int)$id, $userArray);

        return ApiResponse::success($result, 'Xử lý tương tác bài viết thành công.');
    }
}