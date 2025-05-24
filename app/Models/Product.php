<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    //

    //fillable
    protected $fillable = [
        'name',
        'sku',
        'product_categories_id',
        'unit_id',
        'price',
        'qty',
        'min_stock',
    ];

    //relation

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_categories_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    //scope


    public function scopeFilter($query)
    {
        $columns = ['name', 'sku'];
        $query->when(request()->filled('search') ?? false, function ($query) use ($columns) {
            $s = request()->search;
            $query->where(function ($query) use ($columns, $s) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $s . '%');
                }
            });
        });

        //filter by category
        $query->when(request()->filled('category') ?? false, function ($query) {
            $s = request()->category;
            $query->where('product_categories_id', $s);
        });

        // filter by unit
        $query->when(request()->filled('unit') ?? false, function ($query) {
            $s = request()->unit;
            $query->where('unit_id', $s);
        });

        // // filter by created_at
        // $query->when(request()->filled('created') ?? false, function ($query) {
        //     $s = request()->created;
        //     $date = date('Y-m-d', strtotime(urldecode($s)));
        //     $query->whereDate('created_at', $date);
        // });
    }
}
