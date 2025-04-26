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
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_categories_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
