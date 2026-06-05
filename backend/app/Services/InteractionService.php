<?php

namespace App\Services;

use App\Repositories\Contracts\InteractionRepositoryInterface;

class InteractionService
{
    protected InteractionRepositoryInterface $interactionRepository;

    public function __construct(InteractionRepositoryInterface $interactionRepository)
    {
        $this->interactionRepository = $interactionRepository;
    }

    public function toggleLike(int $articleId, array $user): array
    {
        // Gọi hàm xử lý tương tác đã được tối ưu bên trong Repository
        return $this->interactionRepository->like($articleId, $user);
    }
}