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
}
