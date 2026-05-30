<?php

namespace App\Repositories\Contracts;

interface ArticleRepositoryInterface
{
    public function paginate(array $filters): array;

    public function findById(int $id): ?array;

    public function findSpecsByArticleId(int $id): ?array;

    public function getLatest(int $limit): array;

    public function getTrending(int $limit): array;
}
