<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
            ->leftJoin('Source', 'Article.Source_ID', '=', 'Source.Source_ID')
            ->where('Status', 'published')
            ->orderBy('PublishDate', 'desc')
            ->limit($limit)
            ->get(['Article.*', 'Source.SourceName as source_name']);

        return collect($rows)->map(fn($m) => $this->mapModel($m))->all();
    }

    public function getTrending(int $limit): array
    {
        // Trending defined by ViewCount (most read)
        $rows = Article::query()
            ->leftJoin('Source', 'Article.Source_ID', '=', 'Source.Source_ID')
            ->where('Status', 'published')
            ->orderByDesc('ViewCount')
            ->limit($limit)
            ->get(['Article.*', 'Source.SourceName as source_name']);

        return collect($rows)->map(fn($m) => $this->mapModel($m))->all();
    }

    private function mapModel($m): array
    {
        // Normalize source name
        $sourceName = null;
        if (isset($m->source_name)) {
            $sourceName = $m->source_name;
        } elseif (isset($m->Source_ID) && $m->Source_ID) {
            // fallback: try to fetch source name
            $src = DB::table('Source')->where('Source_ID', $m->Source_ID)->first(['SourceName']);
            $sourceName = $src->SourceName ?? null;
        }

        return [
            'title' => $m->Title ?? null,
            'source' => $sourceName,
            'time' => $m->PublishDate ? (is_string($m->PublishDate) ? $m->PublishDate : $m->PublishDate->toIso8601String()) : null,
            'interaction' => (int) ($m->ViewCount ?? 0),
        ];
    }
}
