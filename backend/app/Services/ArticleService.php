<?php

namespace App\Services;

use App\Repositories\Contracts\ArticleRepositoryInterface;

class ArticleService
{
    public function __construct(private readonly ArticleRepositoryInterface $articles) {}

    public function index(array $filters): array
    {
        return $this->articles->paginate($filters);
    }

    public function show(int $id): ?array
    {
        return $this->articles->findById($id);
    }

    public function homeFeed(array $options = []): array
    {
        $latestLimit = (int) ($options['latest_limit'] ?? 5);
        $trendingLimit = (int) ($options['trending_limit'] ?? 3);
        $poolSize = (int) ($options['features_pool_size'] ?? 25);
        $sampleSize = (int) ($options['features_sample_size'] ?? 10);

        $latest = $this->articles->getLatest($latestLimit);
        $trending = $this->articles->getTrending($trendingLimit);

        $pool = $this->articles->getLatest($poolSize);
        $features = collect($pool)->shuffle()->take($sampleSize)->values()->all();

        return [
            'latest' => $latest,
            'trending' => $trending,
            'features' => $features,
        ];
    }

    public function specs(int $id): ?array
    {
        return $this->articles->findSpecsByArticleId($id);
    }
}
