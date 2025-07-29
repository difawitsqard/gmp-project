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
        'note',
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
        $columns = ['name'];
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

        // filter by frequency
        $query->when(request()->filled('frequency') ?? false, function ($query) {
            $s = request()->frequency;
            $query->where('frequency', $s);
        });
    }


    public function scopeSorting($query)
    {
        $query->when(
            request()->filled('sort_field') && request()->filled('sort_order'),
            function ($query) {
                $field = request()->get('sort_field', 'name');
                $order = request()->get('sort_order', 'ascend') === 'ascend' ? 'asc' : 'desc';
                // Abaikan jika kolom takde
                if (in_array($field, array_merge($this->getFillable(), ['created_at', 'updated_at', 'processed_by', 'uplink_id', 'name', 'total']))) {
                    $query->orderBy($field, $order);
                }
            },
            function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        );
    }
}
