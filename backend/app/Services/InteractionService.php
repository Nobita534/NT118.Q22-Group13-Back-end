<?php

namespace App\Services;

use App\Repositories\Contracts\InteractionRepositoryInterface;

class InteractionService
{
    public function __construct(private readonly InteractionRepositoryInterface $interactions) {}

    public function like(int $articleId, array $user): array
    {
        return $this->interactions->like($articleId, $user);
    }

    public function bookmark(int $articleId, array $user): array
    {
        return $this->interactions->bookmark($articleId, $user);
    }
}
