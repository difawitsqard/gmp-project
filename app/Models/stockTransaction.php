<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'source_type',
        'source_id',
        'created_by',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
