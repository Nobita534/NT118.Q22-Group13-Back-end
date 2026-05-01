<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechbyteArticleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Article')->insert([
            [
                'Article_ID' => 101,
                'Title' => 'Snapdragon 8 Gen 5 real-world performance',
                'Slug' => 'snapdragon-8-gen-5-performance',
                'ThumbnailURL' => 'https://cdn.techbyte.vn/articles/101/thumb.jpg',
                'Original_URL' => 'https://techbyte.vn/articles/101',
                'URL_Hash' => md5('https://techbyte.vn/articles/101'),
                'PublishDate' => '2026-04-26 13:00:00',
                'ViewCount' => 23810,
                'Status' => 'published',
                'Source_ID' => 1,
            ],
            [
                'Article_ID' => 102,
                'Title' => 'iPhone 18 Pro Max camera and battery',
                'Slug' => 'iphone-18-pro-max-camera-battery',
                'ThumbnailURL' => 'https://cdn.techbyte.vn/articles/102/thumb.jpg',
                'Original_URL' => 'https://lab.techbyte.vn/articles/102',
                'URL_Hash' => md5('https://lab.techbyte.vn/articles/102'),
                'PublishDate' => '2026-04-25 09:00:00',
                'ViewCount' => 14500,
                'Status' => 'published',
                'Source_ID' => 2,
            ],
        ]);

        DB::table('Article_Content')->insert([
            [
                'Article_ID' => 101,
                'ContentHTML' => '<p>Benchmark results and thermals for Snapdragon 8 Gen 5.</p>',
                'CleanText' => 'Benchmark results and thermals for Snapdragon 8 Gen 5.',
            ],
            [
                'Article_ID' => 102,
                'ContentHTML' => '<p>Camera samples and battery rundown for iPhone 18 Pro Max.</p>',
                'CleanText' => 'Camera samples and battery rundown for iPhone 18 Pro Max.',
            ],
        ]);

        DB::table('Article_Category_Map')->insert([
            ['Category_ID' => 1, 'Article_ID' => 101, 'Is_Primary' => true],
            ['Category_ID' => 3, 'Article_ID' => 101, 'Is_Primary' => false],
            ['Category_ID' => 2, 'Article_ID' => 102, 'Is_Primary' => true],
        ]);

        DB::table('Article_Tags_Map')->insert([
            ['Tag_ID' => 1, 'Article_ID' => 101],
            ['Tag_ID' => 2, 'Article_ID' => 101],
            ['Tag_ID' => 3, 'Article_ID' => 102],
            ['Tag_ID' => 4, 'Article_ID' => 102],
        ]);

        DB::table('Article_Product_Map')->insert([
            ['Product_ID' => 55, 'Article_ID' => 101],
            ['Product_ID' => 56, 'Article_ID' => 102],
        ]);
    }
}
