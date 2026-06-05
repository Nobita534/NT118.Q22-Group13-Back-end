<?php

namespace App\Repositories\Contracts;

interface BookmarkRepositoryInterface
{
    public function bookmark(int $articleId, array $user): array;

    public function getBookmarkedArticles(array $user): array;

    public function getBookmarkedArticlesCount(array $user): int;

    public function bookmarksCount(int $articleId): int;
}
