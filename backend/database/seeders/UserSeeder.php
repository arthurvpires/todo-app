<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'User Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('senha123'),
            'is_admin' => true,
        ]);

        User::factory()->count(10)->create();
    }
}
