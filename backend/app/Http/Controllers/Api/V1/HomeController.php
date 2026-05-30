<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'features_sample_size' => 'sometimes|integer|min:1|max:10',
            'features_pool_size' => 'sometimes|integer|min:1|max:50',
            'latest_limit' => 'sometimes|integer|min:1|max:10',
            'trending_limit' => 'sometimes|integer|min:1|max:10',
        ]);

        $options = [
            'features_sample_size' => $validated['features_sample_size'] ?? 10,
            'features_pool_size' => $validated['features_pool_size'] ?? 25,
            'latest_limit' => $validated['latest_limit'] ?? 5,
            'trending_limit' => $validated['trending_limit'] ?? 3,
        ];

        $payload = $this->articles->homeFeed($options);

        return response()->json($payload);
    }
}
