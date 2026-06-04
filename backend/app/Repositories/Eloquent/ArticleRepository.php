<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function paginate(array $filters): array
    {
        // Minimal implementation to satisfy interface — keep existing behavior for later
        $page = max((int) ($filters['page'] ?? 1), 1);
        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 50);

        $query = Article::query()->where('Status', 'published')->orderBy('PublishDate', 'desc');

        $total = $query->count();

        $items = $query->forPage($page, $perPage)->get()->map(function (Article $model) {
            return $this->mapModel($model);
        })->all();

        return [
            'items' => $items,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'has_next_page' => $page < (int) ceil($total / $perPage),
                'next_page' => $page < (int) ceil($total / $perPage) ? $page + 1 : null,
            ],
        ];
    }

    public function findById(int $id): ?array
    {
        $model = Article::where('Article_ID', $id)->first();

        return $model ? $this->mapModel($model) : null;
    }

    public function findSpecsByArticleId(int $id): ?array
    {
        // Not implemented here; keep null for now
        return null;
    }

    public function getLatest(int $limit): array
    {
        $rows = Article::query()
            ->where('Status', 'published')
            ->orderBy('PublishDate', 'desc')
            ->limit($limit)
            ->get(['Article.*']);

        return collect($rows)->map(fn($m) => $this->mapModel($m))->all();
    }

    public function getTrending(int $limit): array
    {
        // Trending defined by ViewCount (most read)
        $rows = Article::query()
            ->where('Status', 'published')
            ->orderByDesc('ViewCount')
            ->limit($limit)
            ->get(['Article.*']);

        return collect($rows)->map(fn($m) => $this->mapModel($m))->all();
    }

    public function findByIdWithContent(int $id): ?array
    {
        $row = Article::query()
            ->leftJoin('Article_Content', 'Article.Article_ID', '=', 'Article_Content.Article_ID')
            ->where('Article.Article_ID', $id)
            ->first(['Article.*', 'Article_Content.ContentHTML as content_html', 'Article_Content.CleanText as content_clean', 'Article_Content.Sum_content as Summary_text']);

        if (! $row) {
            return null;
        }

        return [
            'id' => $row->Article_ID,
            'title' => $row->Title ?? null,
            'slug' => $row->Slug ?? null,
            'summary_text' => $row->Summary_text ?? null,
            'thumbnail_url' => $row->ThumbnailURL ?? null,
            'source' => $row->Original_URL ?? null,
            'categories' => [],
            'tags' => [],
            'products' => [],
            'content' => [
                'html' => $row->content_html ?? null,
                'clean_text' => $row->content_clean ?? null,
            ],
            'published_at' => $row->PublishDate ? (is_string($row->PublishDate) ? $row->PublishDate : $row->PublishDate->toIso8601String()) : null,
            'updated_at' => $row->updated_at ?? null,
            'stats' => ['views' => (int) ($row->ViewCount ?? 0), 'comments_count' => 0, 'likes' => 0, 'bookmarks' => 0],
        ];
    }

    public function findManyByIdsWithContent(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $rows = Article::query()
            ->leftJoin('Article_Content', 'Article.Article_ID', '=', 'Article_Content.Article_ID')
            ->whereIn('Article.Article_ID', $ids)
            ->get(['Article.*', 'Article_Content.ContentHTML as content_html', 'Article_Content.CleanText as content_clean']);

        return collect($rows)->map(function ($row) {
            return [
                'id' => $row->Article_ID,
                'title' => $row->Title ?? null,
                'slug' => $row->Slug ?? null,
                'summary' => null,
                'summary_text' => $row->SummaryText ?? ($row->Summary_Text ?? ($row->Summary ?? null)),
                'thumbnail_url' => $row->ThumbnailURL ?? null,
                'source' => $row->Original_URL ?? null,
                'categories' => [],
                'tags' => [],
                'products' => [],
                'content' => $row->content_clean ?? ($row->content_html ?? ''),
                'published_at' => $row->PublishDate ? (is_string($row->PublishDate) ? $row->PublishDate : $row->PublishDate->toIso8601String()) : null,
                'updated_at' => $row->updated_at ?? null,
                'stats' => ['views' => (int) ($row->ViewCount ?? 0), 'comments_count' => 0, 'likes' => 0, 'bookmarks' => 0],
            ];
        })->all();
    }

    public function getRelatedArticleIds(array $article, int $limit = 3): array
    {
        // Simple related: pick most recent published articles excluding current
        $query = Article::query()
            ->where('Status', 'published')
            ->where('Article_ID', '!=', $article['id'] ?? 0)
            ->orderBy('PublishDate', 'desc')
            ->limit($limit)
            ->pluck('Article_ID')
            ->all();

        return $query ?: [];
    }

    private function mapModel($m): array
    {
        // Thumbnail: prefer explicit ThumbnailURL column when available
        $thumbnail = $m->ThumbnailURL ?? $m->thumbnail_url ?? null;

        return [
            'id' => (int) ($m->Article_ID ?? 0),
            'title' => $m->Title ?? null,
            'slug' => $m->Slug ?? null,
            'summary' => $m->Summary_text ?? $m->SummaryText ?? null,
            'summary_text' => $m->Summary_text ?? $m->SummaryText ?? null,
            'source' => $m->Original_URL ?? null,
            'time' => $m->PublishDate ? (is_string($m->PublishDate) ? $m->PublishDate : $m->PublishDate->toIso8601String()) : null,
            'interaction' => (int) ($m->ViewCount ?? 0),
            'thumbnail' => $thumbnail,
            'thumbnail_url' => $thumbnail,
            'stats' => ['comments_count' => 0, 'likes' => 0, 'bookmarks' => 0],
        ];
    }
}
