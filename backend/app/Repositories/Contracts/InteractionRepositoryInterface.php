<?php

namespace App\Repositories\Contracts;

interface InteractionRepositoryInterface
{
    public function like(int $articleId, array $user): array;

    public function likesCount(int $articleId): int;
}