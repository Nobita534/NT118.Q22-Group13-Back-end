<?php

namespace App\Repositories\Fake;

use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class FakeCommentRepository implements CommentRepositoryInterface
{
    public function paginateByArticleId(int $articleId, int $page = 1, int $perPage = 10): array
    {
        $comments = collect($this->comments($articleId));
        $start = max($page - 1, 0) * $perPage;

        return [
            'items' => $comments->slice($start, $perPage)->values()->all(),
            'total' => $comments->count(),
        ];
    }

    public function create(int $articleId, array $user, string $content): array
    {
        $comments = $this->comments($articleId);

        $comment = [
            'id' => (count($comments) + 1) + ($articleId * 1000),
            'article_id' => $articleId,
            'user' => ['id' => $user['id'], 'name' => $user['name']],
            'content' => $content,
            'status' => 'published',
            'created_at' => now()->toIso8601String(),
        ];

        $comments[] = $comment;

        Cache::put($this->key($articleId), $comments, now()->addHours(12));

        return $comment;
    }

    public function countByArticleId(int $articleId): int
    {
        return count($this->comments($articleId));
    }

    private function comments(int $articleId): array
    {
        return Cache::remember($this->key($articleId), now()->addHours(12), function () use ($articleId) {
            return match ($articleId) {
                101 => [
                    [
                        'id' => 9001,
                        'article_id' => 101,
                        'user' => ['id' => 1, 'name' => 'Nguyen Van A'],
                        'content' => 'Bài viết rất chi tiết.',
                        'status' => 'published',
                        'created_at' => '2026-04-29T08:10:00Z',
                    ],
                ],
                default => [],
            };
        });
    }

    private function key(int $articleId): string
    {
        return 'techbyte:comments:' . $articleId;
    }
}
