<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{

    protected $fillable = [
        'image_path',
    ];

    protected $appends = [
        'image_url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            $filePath = public_path('uploads/' . $this->image_path);
            if (file_exists($filePath)) {
                return asset('uploads/' . $this->image_path);
            }
        }

        return null;
    }
}
