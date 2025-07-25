<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    // fillable
    protected $fillable = [
        'name',
        'forecasted_at',
        'frequency',
        'horizon',
        'model',
        'input_start_date',
        'input_end_date',
        'status',
        'created_by',
    ];

    // relation
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function analyses()
    {
        return $this->hasMany(ForecastAnalysis::class);
    }

    public function results()
    {
        return $this->hasMany(ForecastResult::class);
    }

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
    }
}
