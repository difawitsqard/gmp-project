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
        $columns = ['name'];
        $query->when(request()->filled('q') ?? false, function ($query) use ($columns) {
            $s = request()->q;
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

        // // filter by created_at
        // $query->when(request()->filled('created') ?? false, function ($query) {
        //     $s = request()->created;
        //     $date = date('Y-m-d', strtotime(urldecode($s)));
        //     $query->whereDate('created_at', $date);
        // });
    }
}
