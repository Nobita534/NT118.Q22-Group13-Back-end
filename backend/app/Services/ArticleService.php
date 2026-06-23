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

    public function show(int $id, ?array $user = null): ?array
    {
        // try to get full article with content
        if (method_exists($this->articles, 'findByIdWithContent')) {
            $article = $this->articles->findByIdWithContent($id, $user);
        } else {
            $article = $this->articles->findById($id);
        }

        if (! $article) {
            return null;
        }

        // ensure content plain text exists
        $clean = $article['content']['clean_text'] ?? null;
        $article['summary_text'] = $article['summary_text'] ?? ($article['summary'] ?? null);
        $article['content'] = $clean;

        // remove viewcount / interaction fields from top-level response
        if (isset($article['stats'])) {
            unset($article['stats']['views']);
        }
        if (isset($article['interaction'])) {
            unset($article['interaction']);
        }

        // related articles: try to get ids then full payloads
        $related = [];
        if (method_exists($this->articles, 'getRelatedArticleIds') && method_exists($this->articles, 'findManyByIdsWithContent')) {
            $ids = $this->articles->getRelatedArticleIds($article, (int) config('article.related_limit', 3));
            if (! empty($ids)) {
                $relatedRows = $this->articles->findManyByIdsWithContent($ids);
                foreach ($relatedRows as $r) {
                    $cleanR = $r['content']['clean_text'] ?? null;
                    $r['summary_text'] = $r['summary_text'] ?? ($r['summary'] ?? null);
                    $r['content'] = $cleanR;
                    if (isset($r['stats'])) {
                        unset($r['stats']['views']);
                    }
                    if (isset($r['interaction'])) {
                        unset($r['interaction']);
                    }
                    $related[] = $r;
                }
            }
        }

        $article['related'] = $related;

        return $article;
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
