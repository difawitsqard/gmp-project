<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastAnalysis extends Model
{
    // fillable
    protected $fillable = [
        'forecast_id',
        'product_id',
        'analysis',
    ];

    protected $casts = [
        'analysis' => 'array',
    ];

    // relation
    public function forecast()
    {
        return $this->belongsTo(Forecast::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
