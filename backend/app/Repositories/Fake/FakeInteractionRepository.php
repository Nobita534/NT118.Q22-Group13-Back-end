<?php

namespace App\Repositories\Fake;

use App\Repositories\Contracts\InteractionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class FakeInteractionRepository implements InteractionRepositoryInterface
{
    public function like(int $articleId, array $user): array
    {
        $likes = $this->likes($articleId);
        $alreadyLiked = in_array($user['id'], $likes, true);

        if (! $alreadyLiked) {
            $likes[] = $user['id'];
            Cache::put($this->likeKey($articleId), $likes, now()->addHours(12));
        }

        return [
            'already_liked' => $alreadyLiked,
            'likes_count' => count($likes),
        ];
    }

    public function bookmark(int $articleId, array $user): array
    {
        $bookmarks = $this->bookmarks($articleId);
        $alreadyBookmarked = in_array($user['id'], $bookmarks, true);

        if (! $alreadyBookmarked) {
            $bookmarks[] = $user['id'];
            Cache::put($this->bookmarkKey($articleId), $bookmarks, now()->addHours(12));
        }

        return [
            'already_bookmarked' => $alreadyBookmarked,
            'bookmarks_count' => count($bookmarks),
        ];
    }

    public function likesCount(int $articleId): int
    {
        return count($this->likes($articleId));
    }

    public function bookmarksCount(int $articleId): int
    {
        return count($this->bookmarks($articleId));
    }

    private function likes(int $articleId): array
    {
        return Cache::remember($this->likeKey($articleId), now()->addHours(12), function () use ($articleId) {
            return match ($articleId) {
                101 => [1, 2],
                default => [],
            };
        });
    }

    private function bookmarks(int $articleId): array
    {
        return Cache::remember($this->bookmarkKey($articleId), now()->addHours(12), function () use ($articleId) {
            return match ($articleId) {
                101 => [1],
                default => [],
            };
        });
    }

    private function likeKey(int $articleId): string
    {
        return 'techbyte:likes:' . $articleId;
    }

    private function bookmarkKey(int $articleId): string
    {
        return 'techbyte:bookmarks:' . $articleId;
    }
}
