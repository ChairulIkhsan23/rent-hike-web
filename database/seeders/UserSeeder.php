<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Random Dummy
        User::factory()->count(10)->create();

        // Admin
        User::factory()->create([
            'name' => 'Admin RentHike',
            'email' => 'admin@renthike.test',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);
    }
}
