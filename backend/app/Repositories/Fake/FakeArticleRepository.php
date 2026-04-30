<?php

namespace App\Repositories\Fake;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Repositories\Contracts\InteractionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class FakeArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private readonly CommentRepositoryInterface $comments,
        private readonly InteractionRepositoryInterface $interactions,
    ) {}

    public function paginate(array $filters): array
    {
        $page = max((int) ($filters['page'] ?? 1), 1);
        $perPage = min(max((int) ($filters['per_page'] ?? 10), 1), 50);

        $items = collect($this->articles())->map(function (array $article) {
            $article['stats']['comments_count'] = $this->comments->countByArticleId($article['id']);
            $article['stats']['likes'] = $this->interactions->likesCount($article['id']);

            return $article;
        })->values();

        $start = ($page - 1) * $perPage;
        $slice = $items->slice($start, $perPage)->values();

        return [
            'items' => $slice->all(),
            'pagination' => [
                'total' => $items->count(),
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($items->count() / $perPage),
                'has_next_page' => $page < (int) ceil($items->count() / $perPage),
                'next_page' => $page < (int) ceil($items->count() / $perPage) ? $page + 1 : null,
            ],
        ];
    }

    public function findById(int $id): ?array
    {
        $article = collect($this->articles())->firstWhere('id', $id);

        if (! $article) {
            return null;
        }

        $article['stats']['comments_count'] = $this->comments->countByArticleId($article['id']);
        $article['stats']['likes'] = $this->interactions->likesCount($article['id']);
        $article['stats']['bookmarks'] = $this->interactions->bookmarksCount($article['id']);
        $article['comments'] = $this->comments->paginateByArticleId($article['id'], 1, 3)['items'];

        return $article;
    }

    public function findSpecsByArticleId(int $id): ?array
    {
        $article = collect($this->articles())->firstWhere('id', $id);

        if (! $article) {
            return null;
        }

        return [
            'article_id' => $id,
            'products' => [
                [
                    'product_id' => 55,
                    'product_name' => 'Snapdragon 8 Gen 5',
                    'brand' => 'Qualcomm',
                    'specs' => [
                        ['name' => 'cpu_single', 'value' => 2010, 'unit' => 'score'],
                        ['name' => 'cpu_multi', 'value' => 6850, 'unit' => 'score'],
                        ['name' => 'gpu', 'value' => 15420, 'unit' => 'score'],
                    ],
                ],
            ],
            'chart' => [
                'dimensions' => ['cpu_single', 'cpu_multi', 'gpu'],
                'series' => [
                    ['label' => 'Snapdragon 8 Gen 5', 'values' => [2010, 6850, 15420]],
                ],
                'unit' => 'score',
            ],
        ];
    }

    private function articles(): array
    {
        return [
            [
                'id' => 101,
                'title' => 'Snapdragon 8 Gen 5: hiệu năng thực tế',
                'slug' => 'snapdragon-8-gen-5-hieu-nang-thuc-te',
                'summary' => 'Bài test hiệu năng CPU/GPU và nhiệt độ trong tác vụ nặng.',
                'thumbnail_url' => 'https://cdn.techbyte.vn/articles/101/thumb.jpg',
                'source' => ['id' => 3, 'name' => 'TechByte', 'website_url' => 'https://techbyte.vn'],
                'categories' => [
                    ['id' => 5, 'name' => 'Mobile', 'slug' => 'mobile', 'is_primary' => true],
                    ['id' => 9, 'name' => 'Chipset', 'slug' => 'chipset', 'is_primary' => false],
                ],
                'tags' => [
                    ['id' => 2, 'name' => 'benchmark'],
                    ['id' => 11, 'name' => 'android'],
                ],
                'products' => [
                    [
                        'id' => 55,
                        'product_name' => 'Snapdragon 8 Gen 5',
                        'brand' => ['id' => 4, 'name' => 'Qualcomm'],
                    ],
                ],
                'content' => [
                    'html' => '<p>Đây là nội dung HTML của bài viết.</p>',
                    'clean_text' => 'Đây là nội dung thuần của bài viết.',
                ],
                'published_at' => '2026-04-26T13:00:00Z',
                'updated_at' => '2026-04-27T03:10:00Z',
                'stats' => ['views' => 23810, 'comments_count' => 0, 'likes' => 0, 'bookmarks' => 0],
            ],
            [
                'id' => 102,
                'title' => 'iPhone 18 Pro Max: camera và pin',
                'slug' => 'iphone-18-pro-max-camera-va-pin',
                'summary' => 'Tổng hợp camera, pin và trải nghiệm thực tế.',
                'thumbnail_url' => 'https://cdn.techbyte.vn/articles/102/thumb.jpg',
                'source' => ['id' => 4, 'name' => 'TechByte Lab', 'website_url' => 'https://lab.techbyte.vn'],
                'categories' => [
                    ['id' => 6, 'name' => 'Review', 'slug' => 'review', 'is_primary' => true],
                ],
                'tags' => [
                    ['id' => 4, 'name' => 'ios'],
                    ['id' => 8, 'name' => 'camera'],
                ],
                'products' => [],
                'content' => [
                    'html' => '<p>Nội dung bài review.</p>',
                    'clean_text' => 'Nội dung bài review.',
                ],
                'published_at' => '2026-04-25T09:00:00Z',
                'updated_at' => '2026-04-25T11:00:00Z',
                'stats' => ['views' => 14500, 'comments_count' => 0, 'likes' => 0, 'bookmarks' => 0],
            ],
        ];
    }
}
