<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use App\Support\ApiResponse;

class SpecsController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function show(int $id)
    {
        $specs = $this->articles->specs($id);

        if (! $specs) {
            return ApiResponse::error('Article not found.', 404, 'RESOURCE_NOT_FOUND');
        }

        return ApiResponse::success($specs, 'Article specs fetched successfully.');
    }
}
