<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'stock_before',
        'stock_after',
        'source_type',
        'source_id',
        'created_by',
        'note',
        'batch_reference',
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

    public function scopeByBatch($query, $batchReference)
    {
        return $query->where('batch_reference', $batchReference);
    }

    public function scopeFilter($query)
    {
        $columns = ['note'];
        $query->when(request()->filled('stock_search') ?? false, function ($query) use ($columns) {
            $s = request()->stock_search;

            $query->where(function ($query) use ($columns, $s) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $s . '%');
                }
            });

            // filter juga berdasarakan search tapi ke product.name
            $columns = ['name', 'sku'];
            $query->orWhereHas('product', function ($q) use ($s, $columns) {
                $q->where(function ($q) use ($s, $columns) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', '%' . $s . '%');
                    }
                });
            });

            $query->orWhereHas('createdBy', function ($q) use ($s) {
                $q->where('name', 'like', '%' . $s . '%');
            });
        });
    }

    public function scopeSorting($query)
    {
        $query->when(request()->filled('stock_sort') ?? false, function ($query) {
            $sort = request()->stock_sort;

            if ($sort === 'newest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($sort === 'latest') {
                $query->orderBy('created_at', 'asc');
            }
        });

        if (!request()->filled('sort')) {
            $query->orderBy('created_at', 'desc');
        }
    }
}
