<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'name',
    ];

    //relation
    public function products()
    {
        return $this->hasMany(Product::class, 'product_categories_id');
    }
}
