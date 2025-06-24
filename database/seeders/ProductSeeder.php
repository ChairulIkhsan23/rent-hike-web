<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
    */
    public function run(): void
    {
        Product::factory()->count(20)->create();
    }
}
