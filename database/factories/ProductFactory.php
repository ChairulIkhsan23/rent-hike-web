<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->randomElement([
                'Tenda Dome Kapasitas 4 Orang',
                'Tenda Tunnel Waterproof',
                'Sleeping Bag Polar',
                'Sleeping Bag Ultralight',
                'Kompor Portable Mini Gas',
                'Kompor Lapangan Lipat',
                'Matras Aluminium',
                'Matras Lipat Eva Foam',
                'Carrier 65 Liter',
                'Daypack 25 Liter',
                'Trekking Pole Adjustable',
                'Headlamp Waterproof',
                'Senter LED Tactical',
                'Nest Hammock Single',
                'Cooking Set Aluminium',
                'Gas Kaleng Butane',
                'Jas Hujan Ponco Outdoor',
                'Gaiter Anti Lintah',
                'Sepatu Gunung High Cut',
                'Rain Cover Ransel 60L',
            ]),

            'deskripsi' => $this->faker->sentence(),
            'harga_sewa_per_hari' => $this->faker->numberBetween(20000, 100000),
            'kategori_id' => Category::inRandomOrder()->value('id'),
            'stok' => $this->faker->numberBetween(1, 10),
            'foto' => 'assets/product-image/foto-produk.jpg'
        ];
    }
}
