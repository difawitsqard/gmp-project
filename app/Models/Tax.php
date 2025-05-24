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

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_taxes')->withPivot('amount');
    }
}
