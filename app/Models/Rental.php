<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tanggal_mulai', 'tanggal_selesai', 'total_harga', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rentalitems()
    {
        return $this->hasMany(RentalItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
