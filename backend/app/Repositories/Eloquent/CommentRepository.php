<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CommentRepository implements CommentRepositoryInterface
{
    public function paginateByArticleId(int $articleId, int $page = 1, int $perPage = 10): array
    {
        $page = max($page, 1);
        $perPage = min(max($perPage, 1), 50);

        $query = DB::table('Comments')
            ->leftJoin('User', 'Comments.User_ID', '=', 'User.User_ID')
            ->where('Comments.Article_ID', $articleId)
            ->orderByDesc('Comments.CreatedAt');

        $total = (clone $query)->count();

        $items = $query
            ->forPage($page, $perPage)
            ->get([
                'Comments.Comment_ID',
                'Comments.Article_ID',
                'Comments.User_ID',
                'Comments.Content',
                'Comments.CreatedAt',
                'User.Username',
                'User.Avatar',
            ])
            ->map(fn ($row) => $this->mapRow($row))
            ->all();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function create(int $articleId, array $user, string $content): array
    {
        $id = DB::table('Comments')->insertGetId([
            'Article_ID' => $articleId,
            'User_ID' => $user['id'],
            'Content' => $content,
            'CreatedAt' => now(),
        ], 'Comment_ID');

        $row = DB::table('Comments')
            ->leftJoin('User', 'Comments.User_ID', '=', 'User.User_ID')
            ->where('Comments.Comment_ID', $id)
            ->first([
                'Comments.Comment_ID',
                'Comments.Article_ID',
                'Comments.User_ID',
                'Comments.Content',
                'Comments.CreatedAt',
                'User.Username',
                'User.Avatar',
            ]);

        return $this->mapRow($row);
    }

    public function countByArticleId(int $articleId): int
    {
        return DB::table('Comments')
            ->where('Article_ID', $articleId)
            ->count();
    }

    private function mapRow(object $row): array
    {
        return [
            'id' => (int) $row->Comment_ID,
            'article_id' => (int) $row->Article_ID,
            'user' => [
                'id' => $row->User_ID ? (int) $row->User_ID : null,
                'name' => $row->Username,
                'avatar' => $row->Avatar,
            ],
            'content' => $row->Content,
            'status' => 'published',
            'created_at' => $row->CreatedAt,
        ];
    }
}
