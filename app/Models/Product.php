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
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_categories_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // pengurangan stok
    public function decreaseStock(int $qty, ?Model $source = null, ?string $note = null): void
    {
        if ($this->qty < $qty) {
            throw new \Exception('Stok tidak cukup');
        }

        $this->qty -= $qty;
        $this->save();

        $this->stockTransactions()->create([
            'qty' => $qty,
            'type' => 'in',
            'note' => $note,
            'source_id' => $source?->id,
            'source_type' => $source ? get_class($source) : null,
            'created_by' => auth()->id(),
        ]);
    }

    // penambahan stok
    public function increaseStock(int $qty, ?Model $source = null, ?string $note = null): void
    {
        $this->qty += $qty;
        $this->save();

        $this->stockTransactions()->create([
            'qty' => $qty,
            'type' => 'out',
            'note' => $note,
            'source_id' => $source?->id,
            'source_type' => $source ? get_class($source) : null,
            'created_by' => auth()->id(),
        ]);
    }

    //scope
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

        //filter by category
        $query->when(request()->filled('category') ?? false, function ($query) {
            $s = request()->category;
            $query->where('product_categories_id', $s);
        });

        // filter by unit
        $query->when(request()->filled('unit') ?? false, function ($query) {
            $s = request()->unit;
            $query->where('unit_id', $s);
        });

        // Filter produk berdasarkan tanggal order item
        $query->when(request()->filled('order_date_start') ?? false, function ($query) {
            $startDate = request()->order_date_start;
            $query->whereHas('orderItems', function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)));
            });
        });

        // Filter untuk batas akhir tanggal (opsional)
        $query->when(request()->filled('order_date_end') ?? false, function ($query) {
            $endDate = request()->order_date_end;
            $query->whereHas('orderItems', function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($endDate)));
            });
        });

        // // filter by created_at
        // $query->when(request()->filled('created') ?? false, function ($query) {
        //     $s = request()->created;
        //     $date = date('Y-m-d', strtotime(urldecode($s)));
        //     $query->whereDate('created_at', $date);
        // });
    }
}
