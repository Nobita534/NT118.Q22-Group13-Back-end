<?php

namespace App\Repositories\Contracts;

interface ArticleRepositoryInterface
{
    public function paginate(array $filters): array;

    public function findById(int $id): ?array;

    // Return article array including content (clean text and HTML) suitable for API output
    public function findByIdWithContent(int $id, ?array $user = null): ?array;

    // Batch fetch articles by ids with content
    public function findManyByIdsWithContent(array $ids): array;

    // Return related article ids for given article array (business-level candidate selection)
    public function getRelatedArticleIds(array $article, int $limit = 3): array;

    public function findSpecsByArticleId(int $id): ?array;

    public function getLatest(int $limit): array;

    public function getTrending(int $limit): array;
}
