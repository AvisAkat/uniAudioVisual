<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@avbms.com'],
            [
                'name' => 'Admin',
                'university_id' => '99999999',
                'password' => Hash::make('admin@123'),
                'role' => 'admin',
            ]
        );
    }
}
