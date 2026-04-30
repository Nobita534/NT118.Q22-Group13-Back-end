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

    public function show(int $id): ?array
    {
        return $this->articles->findById($id);
    }

    public function specs(int $id): ?array
    {
        return $this->articles->findSpecsByArticleId($id);
    }
}
