<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_harga',
        'status',
    ];

    protected static function booted(): void
    {
        static::updating(function (Rental $rental) {
            $originalStatus = $rental->getOriginal('status');
            $newStatus = $rental->status;

            // Jika tidak ada perubahan status, skip
            if ($originalStatus === $newStatus) {
                return;
            }

            // Dari dibayar → dipinjam → kurangi stok
            if ($originalStatus === 'dibayar' && $newStatus === 'dipinjam') {
                self::kurangiStok($rental);
            }

            // Dari dipinjam → selesai atau dibatalkan → kembalikan stok
            if ($originalStatus === 'dipinjam' && in_array($newStatus, ['selesai', 'dibatalkan'])) {
                self::kembalikanStok($rental);
            }
        });
    }

    protected static function kurangiStok(Rental $rental): void
    {
        foreach ($rental->rentalItems as $item) {
            $product = $item->product;
            if ($product && $product->stok >= $item->jumlah) {
                $product->decrement('stok', $item->jumlah);
            }
        }
    }

    protected static function kembalikanStok(Rental $rental): void
    {
        foreach ($rental->rentalItems as $item) {
            $product = $item->product;
            if ($product) {
                $product->increment('stok', $item->jumlah);
            }
        }
    }

    // ======================
    // Relationships
    // ======================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rentalItems()
    {
        return $this->hasMany(RentalItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
