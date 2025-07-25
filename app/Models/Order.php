<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            do {
                $uuid = self::generateCustomUUID();
            } while (self::where('uuid', $uuid)->exists());

            $model->uuid = $uuid;
        });
    }

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'address',
        'shipping_method',
        'shipping_fee',
        'sub_total',
        'total',
        'note',
        'status',
        'uplink_id',
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

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function uplink()
    {
        return $this->belongsTo(User::class, 'uplink_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
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

    public function scopeSorting($query)
    {
        $query->when(
            request()->filled('sort_field') && request()->filled('sort_order'),
            function ($query) {
                $field = request()->get('sort_field', 'name');
                $order = request()->get('sort_order', 'ascend') === 'ascend' ? 'asc' : 'desc';
                // Abaikan jika kolom takde
                if (in_array($field, array_merge($this->getFillable(), ['created_at', 'updated_at', 'processed_by', 'uplink_id', 'name', 'total']))) {
                    $query->orderBy($field, $order);
                }
            },
            function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        );
    }

    // Check if order can be cancelled
    public function getCanbeCancelledAttribute()
    {
        return in_array($this->status, ['pending', 'waiting_confirmation']) &&
            (
                !$this->latestPayment ||
                $this->latestPayment->status == 'pending'
            );
    }

    // Chek if order can change payment method
    public function getCanbeChangePaymentMethodAttribute()
    {
        return $this->status == 'pending' && $this->latestPayment && $this->latestPayment->status == 'pending' && $this->latestPayment->payment_type != null;
    }


    public static function generateCustomUUID($prefix = 'ORD')
    {
        $random = strtoupper(bin2hex(random_bytes(6)));
        return $prefix . substr($random, 0, 12);
    }
}
