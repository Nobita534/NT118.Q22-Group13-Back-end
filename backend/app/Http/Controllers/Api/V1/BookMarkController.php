<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookmarkRequest;
use App\Services\BookmarkService;
use App\Support\ApiResponse;

class BookmarkController extends Controller
{
    protected BookmarkService $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function bookmark(BookmarkRequest $request, $id)
    {
        // Lấy mảng thông tin User từ custom middleware
        $user = $request->attributes->get('api_user'); 

        // dd($user);

        $result = $this->bookmarkService->toggleBookmark((int)$id, $user);; 

        return ApiResponse::success($result, 'Xử lý trạng thái bookmark thành công.');
    }
}