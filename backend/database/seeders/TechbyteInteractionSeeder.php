<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechbyteInteractionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Comments')->insert([
            [
                'Comment_ID' => 9001,
                'Content' => 'Very detailed breakdown.',
                'CreatedAt' => '2026-04-29 08:10:00',
                'Article_ID' => 101,
                'User_ID' => 1,
            ],
            [
                'Comment_ID' => 9002,
                'Content' => 'Great camera samples.',
                'CreatedAt' => '2026-04-29 09:20:00',
                'Article_ID' => 102,
                'User_ID' => 2,
            ],
        ]);

        DB::table('Interactions')->insert([
            [
                'InteractionId' => 1,
                'Type' => 'like',
                'Timestamp' => '2026-04-29 10:00:00',
                'Article_ID' => 101,
                'UserID' => 1,
            ],
            [
                'InteractionId' => 2,
                'Type' => 'like',
                'Timestamp' => '2026-04-29 10:05:00',
                'Article_ID' => 101,
                'UserID' => 2,
            ],
        ]);

        DB::table('Bookmarks')->insert([
            [
                'BookmarkID' => 1,
                'CreateAt' => '2026-04-29 11:00:00',
                'Article_ID' => 101,
                'User_ID' => 1,
            ],
        ]);
    }
}
