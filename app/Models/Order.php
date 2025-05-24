<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'shipping_method',
        'shipping_fee',
        'sub_total',
        'total',
        'status',
        //'uplink_id',
    ];

    protected $appends = [
        'canbe_cancelled',
        'canbe_change_payment_method',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'order_taxes')->withPivot('amount');
    }

    public function scopeFilter($query)
    {
        $columns = ['name', 'id'];
        $query->when(request()->filled('search') ?? false, function ($query) use ($columns) {
            $s = request()->search;
            $query->where(function ($query) use ($columns, $s) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $s . '%');
                }
            });
        });

        // filter by status
        $query->when(request()->filled('status') ?? false, function ($query) {
            $s = request()->status;
            $query->where('status', $s);
        });

        //filter by payment_status
        $query->when(request()->filled('payment_status') ?? false, function ($query) {
            $s = request()->payment_status;
            $query->whereHas('latestPayment', function ($query) use ($s) {
                $query->where('status', $s);
            });
        });

        // filter by created_at
        $query->when(request()->filled('created') ?? false, function ($query) {
            $s = request()->created;
            $date = date('Y-m-d', strtotime(urldecode($s)));
            $query->whereDate('created_at', $date);
        });
    }

    // Check if order can be cancelled
    public function getCanbeCancelledAttribute()
    {
        return $this->status == 'pending' && $this->latestPayment && $this->latestPayment->status == 'pending';
    }

    // Chek if order can change payment method
    public function getCanbeChangePaymentMethodAttribute()
    {
        return $this->status == 'pending' && $this->latestPayment && $this->latestPayment->status == 'pending' && $this->latestPayment->payment_type != null;
    }
}
