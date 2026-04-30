<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ArticleIndexRequest;
use App\Services\ArticleService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(ArticleIndexRequest $request)
    {
        $result = $this->articles->index($request->validated());

        return ApiResponse::paginated(
            $result['items'],
            $result['pagination'],
            'Articles fetched successfully.'
        );
    }

    public function show(int $id)
    {
        $article = $this->articles->show($id);

        if (! $article) {
            return ApiResponse::error('Article not found.', 404, 'RESOURCE_NOT_FOUND');
        }

        return ApiResponse::success($article, 'Article detail fetched successfully.');
    }
}
