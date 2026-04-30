<?php

namespace App\Repositories\Contracts;

interface CommentRepositoryInterface
{
    public function paginateByArticleId(int $articleId, int $page = 1, int $perPage = 10): array;

    public function create(int $articleId, array $user, string $content): array;

    public function countByArticleId(int $articleId): int;
}
