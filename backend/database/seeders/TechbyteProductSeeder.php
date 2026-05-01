<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechbyteProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Product')->insert([
            [
                'Product_ID' => 55,
                'ProductName' => 'Snapdragon 8 Gen 5',
                'Country' => 'US',
                'Brand_ID' => 1,
            ],
            [
                'Product_ID' => 56,
                'ProductName' => 'iPhone 18 Pro Max',
                'Country' => 'US',
                'Brand_ID' => 2,
            ],
        ]);

        DB::table('Specifications')->insert([
            [
                'Spec_ID' => 1,
                'SpecName' => 'cpu_single',
                'SpecValue' => '2010',
                'Product_ID' => 55,
            ],
            [
                'Spec_ID' => 2,
                'SpecName' => 'cpu_multi',
                'SpecValue' => '6850',
                'Product_ID' => 55,
            ],
            [
                'Spec_ID' => 3,
                'SpecName' => 'gpu',
                'SpecValue' => '15420',
                'Product_ID' => 55,
            ],
            [
                'Spec_ID' => 4,
                'SpecName' => 'battery',
                'SpecValue' => '5000mAh',
                'Product_ID' => 56,
            ],
            [
                'Spec_ID' => 5,
                'SpecName' => 'camera',
                'SpecValue' => '48MP main',
                'Product_ID' => 56,
            ],
        ]);
    }
}
