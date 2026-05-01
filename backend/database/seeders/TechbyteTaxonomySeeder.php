<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechbyteTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Brand')->insert([
            ['Brand_ID' => 1, 'BrandName' => 'Qualcomm'],
            ['Brand_ID' => 2, 'BrandName' => 'Apple'],
        ]);

        DB::table('Source')->insert([
            ['Source_ID' => 1, 'SourceName' => 'TechByte', 'WebsiteURL' => 'https://techbyte.vn'],
            ['Source_ID' => 2, 'SourceName' => 'TechByte Lab', 'WebsiteURL' => 'https://lab.techbyte.vn'],
        ]);

        DB::table('Category')->insert([
            ['Category_ID' => 1, 'Name' => 'Mobile', 'Slug' => 'mobile', 'Description' => 'Mobile news and reviews'],
            ['Category_ID' => 2, 'Name' => 'Review', 'Slug' => 'review', 'Description' => 'Hands-on reviews'],
            ['Category_ID' => 3, 'Name' => 'Chipset', 'Slug' => 'chipset', 'Description' => 'SoC and benchmark'],
        ]);

        DB::table('Tags')->insert([
            ['Tag_ID' => 1, 'TagName' => 'benchmark'],
            ['Tag_ID' => 2, 'TagName' => 'android'],
            ['Tag_ID' => 3, 'TagName' => 'ios'],
            ['Tag_ID' => 4, 'TagName' => 'camera'],
        ]);
    }
}
