<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts;
use Illuminate\Support\Facades\DB;

class InteractionRepository implements Contracts\InteractionRepositoryInterface
{
    public function like(int $articleId, array $user): array
    {
        $userId = $user['id'];
        $type = 'like';

        // Kiểm tra lượt tương tác thích bài viết dựa vào đúng Schema "UserID" viết liền
        $interaction = DB::table('Interactions')
            ->where('UserID', $userId)
            ->where('Article_ID', $articleId)
            ->where('Type', $type)
            ->first();

        if ($interaction) {
            // Nếu đã thích thì tiến hành xóa tương tác (Unlike)
            DB::table('Interactions')->where('InteractionId', $interaction->InteractionId)->delete();
            return ['is_liked' => false];
        }

        // Nếu chưa thích thì tiến hành thêm bản ghi mới
        DB::table('Interactions')->insert([
            'UserID' => $userId,
            'Article_ID' => $articleId,
            'Type' => $type,
            'Timestamp' => now(),
        ]);

        return ['is_liked' => true];
    }

    public function likesCount(int $articleId): int
    {
        return DB::table('Interactions')
            ->where('Article_ID', $articleId)
            ->where('Type', 'like')
            ->count();
    }
}