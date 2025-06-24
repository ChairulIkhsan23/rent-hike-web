<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['rental_id', 'metode', 'status', 'midtrans_order_id', 'bukti_pembayaran'];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
