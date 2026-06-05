<?php

namespace App\Repositories\Contracts;

interface BookmarkRepositoryInterface
{
    public function bookmark(int $articleId, array $user): array;

    public function bookmarksCount(int $articleId): int;
}