<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookmarkRequest;
use App\Services\BookmarkService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $user = $request->attributes->get('api_user');
        $articles = $this->bookmarkService->getBookmarkedArticles($user);

        return ApiResponse::success($articles, 'Danh sách bài viết đã lưu.');
    }
    public function count(Request $request)
    {
        $user = $request->attributes->get('api_user');
        $count = $this->bookmarkService->getBookmarkedArticlesCount($user);

        return ApiResponse::success(['count' => $count], 'Số bài viết đã lưu.');
    }
}
