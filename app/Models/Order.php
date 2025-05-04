<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name',
        'sub_total',
        'total',
        'payment_method',
        'status',
        'payment_status',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
