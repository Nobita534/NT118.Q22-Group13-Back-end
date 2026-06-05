<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts;
use Illuminate\Support\Facades\DB;

class BookmarkRepository implements Contracts\BookmarkRepositoryInterface
{
    public function bookmark(int $articleId, array $user): array
    {
        $userId = $user['id'];

        // Kiểm tra xem user đã bookmark bài viết này chưa
        $bookmark = DB::table('Bookmarks')
            ->where('User_ID', $userId)
            ->where('Article_ID', $articleId)
            ->first();

        if ($bookmark) {
            // Nếu đã tồn tại thì tiến hành xóa (Unbookmark)
            DB::table('Bookmarks')->where('BookmarkID', $bookmark->BookmarkID)->delete();
            return ['is_bookmarked' => false];
        }

        // Nếu chưa tồn tại thì tiến hành thêm mới (Bookmark)
        DB::table('Bookmarks')->insert([
            'User_ID' => $userId,
            'Article_ID' => $articleId,
            'CreateAt' => now(),
        ]);

        return ['is_bookmarked' => true];
    }

    public function bookmarksCount(int $articleId): int
    {
        return DB::table('Bookmarks')->where('Article_ID', $articleId)->count();
    }
}