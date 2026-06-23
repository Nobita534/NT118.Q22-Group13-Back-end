<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts;
use Illuminate\Support\Facades\DB;

class BookmarkRepository implements Contracts\BookmarkRepositoryInterface
{
    public function bookmark(int $articleId, array $user): array
    {
        $userId = $user['User_ID'] ?? $user['id'];


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

    public function getBookmarkedArticles(array $user): array
    {
        $userId = $user['User_ID'] ?? $user['id'];

        $rows = DB::table('Bookmarks')
            ->join('Article', 'Bookmarks.Article_ID', '=', 'Article.Article_ID')
            ->leftJoin('Article_Content', 'Article.Article_ID', '=', 'Article_Content.Article_ID')
            ->where('Bookmarks.User_ID', $userId)
            ->orderByDesc('Bookmarks.CreateAt')
            ->get([
                'Article.Article_ID',
                'Article.Title',
                'Article.Slug',
                'Article.Original_URL',
                'Article.PublishDate',
                'Article.ThumbnailURL',
                'Article.ViewCount',
                'Article_Content.CleanText as content_clean',
                'Article_Content.Sum_content as summary_text',
            ]);

        return collect($rows)->map(function ($row) {
            $thumbnail = $row->ThumbnailURL ?? null;

            return [
                'id' => (int) ($row->Article_ID ?? 0),
                'title' => $row->Title ?? null,
                'slug' => $row->Slug ?? null,
                'summary' => $row->summary_text ?? null,
                'summary_text' => $row->summary_text ?? null,
                'content' => $row->content_clean ?? '',
                'source' => $row->Original_URL ?? null,
                'time' => $row->PublishDate ? (is_string($row->PublishDate) ? $row->PublishDate : $row->PublishDate->toIso8601String()) : null,
                'published_at' => $row->PublishDate ? (is_string($row->PublishDate) ? $row->PublishDate : $row->PublishDate->toIso8601String()) : null,
                'thumbnail' => $thumbnail,
                'thumbnail_url' => $thumbnail,
                'stats' => [
                    'views' => (int) ($row->ViewCount ?? 0),
                    'comments_count' => 0,
                    'likes' => 0,
                    'bookmarks' => 0,
                ],
            ];
        })->all();
    }

    public function getBookmarkedArticlesCount(array $user): int
    {
        return DB::table('Bookmarks')
            ->where('User_ID', $user['User_ID'] ?? $user['id'])
            ->count();
    }

    public function bookmarksCount(int $articleId): int
    {
        return DB::table('Bookmarks')->where('Article_ID', $articleId)->count();
    }
}
