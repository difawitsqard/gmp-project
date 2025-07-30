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
        'description',
        'status',
    ];

    //relation
    public function products()
    {
        return $this->hasMany(Product::class, 'product_categories_id');
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

        // filter by created_at
        $query->when(request()->filled('created') ?? false, function ($query) {
            $s = request()->created;
            $date = date('Y-m-d', strtotime(urldecode($s)));
            $query->whereDate('created_at', $date);
        });
    }

    public function scopeSorting($query)
    {
        $query->when(
            request()->filled('sort_field') && request()->filled('sort_order'),
            function ($query) {
                $field = request()->get('sort_field', 'name');
                $order = request()->get('sort_order', 'ascend') === 'ascend' ? 'asc' : 'desc';

                // Total Products
                if ($field === 'items') {
                    $query->withCount('products')->orderBy('products_count', $order);
                    return;
                }

                // Abaikan jika kolom takde
                if (in_array($field, array_merge($this->getFillable(), ['created_at', 'updated_at']))) {
                    $query->orderBy($field, $order);
                }
            }
        );

        $query->when(!request()->filled('sort_field'), function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }
}
