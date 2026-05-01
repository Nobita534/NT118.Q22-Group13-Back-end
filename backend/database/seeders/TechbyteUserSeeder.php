<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TechbyteUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('User')->insert([
            [
                'User_ID' => 1,
                'Username' => 'Editor',
                'Email' => 'editor@techbyte.vn',
                'PasswordHash' => Hash::make('Password@123'),
                'Bio' => 'Tech editor',
                'Avatar' => null,
                'Role' => 'user',
            ],
            [
                'User_ID' => 2,
                'Username' => 'Admin',
                'Email' => 'admin@techbyte.vn',
                'PasswordHash' => Hash::make('Password@123'),
                'Bio' => 'Site admin',
                'Avatar' => null,
                'Role' => 'admin',
            ],
            [
                'User_ID' => 3,
                'Username' => 'Guest',
                'Email' => 'guest@techbyte.vn',
                'PasswordHash' => Hash::make('Password@123'),
                'Bio' => null,
                'Avatar' => null,
                'Role' => 'guest',
            ],
        ]);
    }
}
