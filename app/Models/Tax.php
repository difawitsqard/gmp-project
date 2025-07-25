<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'percent',
        'fixed_amount',
        'status',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_taxes')->withPivot('amount');
    }

    public function scopeFilter($query)
    {
        $columns = ['name'];
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
    }

    public function scopeSorting($query)
    {
        $query->when(
            request()->filled('sort_field') && request()->filled('sort_order'),
            function ($query) {
                $field = request()->get('sort_field', 'name');
                $order = request()->get('sort_order', 'ascend') === 'ascend' ? 'asc' : 'desc';
                // Abaikan jika kolom takde
                if (in_array($field, array_merge($this->getFillable(), ['created_at', 'updated_at']))) {
                    $query->orderBy($field, $order);
                } else {
                    $query->orderBy('name', 'asc');
                }
            },
            function ($query) {
                $query->orderBy('name', 'asc');
            }
        );
    }
}
