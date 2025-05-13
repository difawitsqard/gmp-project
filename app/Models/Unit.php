<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'name',
        'short_name',
        'status',
    ];

    //relation
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    public function scopeFilter($query)
    {
        $columns = ['name', 'short_name'];
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
}
