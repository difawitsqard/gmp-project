<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTax extends Model
{
    protected $fillable = [
        'order_id',
        'tax_id',
        'amount',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
