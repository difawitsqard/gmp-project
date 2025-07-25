<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'midtrans_uuid',
        'payment_type',
        'snap_token',
        'status',
        'paid_at',
        'expired_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
