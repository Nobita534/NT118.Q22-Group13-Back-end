<?php

namespace App\Services;

use App\Repositories\Contracts\BookmarkRepositoryInterface;

class BookmarkService
{
    protected BookmarkRepositoryInterface $bookmarkRepository;

    // Inject Interface thay vì Class cụ thể để đảm bảo tính lỏng lẻo (Loose Coupling)
    public function __construct(BookmarkRepositoryInterface $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    public function toggleBookmark(int $articleId, array $user): array
    {
        // Gọi hàm duy nhất đã ôm trọn logic toggle ở tầng Repository
        return $this->bookmarkRepository->bookmark($articleId, $user);
    }
}