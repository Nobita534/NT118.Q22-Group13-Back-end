<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CommentStoreRequest;
use App\Services\CommentService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $comments) {}

    public function indexByArticle(int $id, Request $request)
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        $result = $this->comments->listByArticle($id, $page, $perPage);

        return ApiResponse::paginated(
            $result['items'],
            [
                'total' => $result['total'],
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) max(1, ceil($result['total'] / max($perPage, 1))),
                'has_next_page' => $page * $perPage < $result['total'],
                'next_page' => $page * $perPage < $result['total'] ? $page + 1 : null,
            ],
            'Comments fetched successfully.'
        );
    }

    public function store(CommentStoreRequest $request)
    {
        $user = $request->attributes->get('api_user');

        if (! $user) {
            return ApiResponse::error('Unauthenticated.', 401, 'AUTH_UNAUTHENTICATED');
        }

        try {
            $comment = $this->comments->store(
                (int) $request->validated('article_id'),
                $user,
                $request->validated('content')
            );
        } catch (\RuntimeException) {
            return ApiResponse::error('Article not found.', 404, 'RESOURCE_NOT_FOUND');
        }

        return ApiResponse::success($comment, 'Comment created successfully.', 201);
    }
}
