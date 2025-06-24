<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
    */
    public function run(): void
    {
        $kategori = [
            'Apparel & Footwear',
            'Shelter & Sleeping',
            'Navigation & Lighting',
            'Cooking Mess Kit',
            'Backpacks & Bags',
            'Safety & Emergency'
        ];

        foreach ($kategori as $nama) {
            Category::create(['nama' => $nama]);
        }
    }
}
