<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['kategori_id', 'nama', 'deskripsi', 'harga_sewa_per_hari', 'stok', 'foto'];

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }
}