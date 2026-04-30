<?php

namespace App\Services;

use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $comments,
        private readonly ArticleRepositoryInterface $articles,
    ) {}

    public function listByArticle(int $articleId, int $page = 1, int $perPage = 10): array
    {
        return $this->comments->paginateByArticleId($articleId, $page, $perPage);
    }

    public function store(int $articleId, array $user, string $content): array
    {
        if (! $this->articles->findById($articleId)) {
            throw new \RuntimeException('Article not found');
        }

        return $this->comments->create($articleId, $user, $content);
    }
}
