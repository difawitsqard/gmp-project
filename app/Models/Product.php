<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    //

    //fillable
    protected $fillable = [
        'name',
        'description',
        'sku',
        'product_categories_id',
        'unit_id',
        'price',
        'qty',
        'min_stock',
    ];

    //appends
    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        $images = $this->images()->first();
        if ($images) {
            return $images->image_url;
        }
        return null;
    }

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
    public function decreaseStock(int $qty, ?Model $source = null, ?string $note = null, ?string $batchReference = null): void
    {
        if ($this->qty < $qty) {
            throw new \Exception('Stok tidak cukup');
        }

        // Simpan stok sebelum perubahan
        $stockBefore = $this->qty;

        // Kurangi stok
        $this->qty -= $qty;
        $this->save();

        // Hitung stok setelah perubahan
        $stockAfter = $this->qty;

        $this->stockTransactions()->create([
            'qty' => $qty,
            'type' => 'out',
            'stock_before' => $stockBefore, // Tambahkan stok sebelum
            'stock_after' => $stockAfter,   // Tambahkan stok sesudah
            'note' => $note,
            'source_id' => $source?->id,
            'source_type' => $source ? get_class($source) : null,
            'created_by' => auth()->id(),
            'batch_reference' => $batchReference,
        ]);
    }

    // penambahan stok
    public function increaseStock(int $qty, ?Model $source = null, ?string $note = null, ?string $batchReference = null): void
    {
        // Simpan stok sebelum perubahan
        $stockBefore = $this->qty;

        // Tambah stok
        $this->qty += $qty;
        $this->save();

        // Hitung stok setelah perubahan
        $stockAfter = $this->qty;

        $this->stockTransactions()->create([
            'qty' => $qty,
            'type' => 'in',
            'stock_before' => $stockBefore, // Tambahkan stok sebelum
            'stock_after' => $stockAfter,   // Tambahkan stok sesudah
            'note' => $note,
            'source_id' => $source?->id,
            'source_type' => $source ? get_class($source) : null,
            'created_by' => auth()->id(),
            'batch_reference' => $batchReference,
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

        $query->when(request()->filled('stock_status') ?? false, function ($query) {
            $status = request()->stock_status;

            if ($status === 'available') {
                // Produk dengan stok tersedia (di atas min_stock)
                $query->whereRaw('qty > min_stock');
            } elseif ($status === 'low') {
                // Produk dengan stok rendah (di antara 0 dan min_stock)
                $query->whereRaw('qty <= min_stock AND qty > 0');
            } elseif ($status === 'out') {
                // Produk dengan stok habis (0)
                $query->where('qty', 0);
            }
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
                if (in_array($field, array_merge($this->getFillable(), ['created_at', 'updated_at']))) {
                    $query->orderBy($field, $order);
                }
            }
        );

        $query->when(request()->filled('sort') ?? false, function ($query) {
            $sort = request()->sort;

            if ($sort === 'newest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($sort === 'latest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sort === 'bestseller') {
                // Sorting berdasarkan total quantity terjual
                $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->select('products.*', DB::raw('COALESCE(SUM(order_items.quantity),0) as total_sold'))
                    ->groupBy('products.id')
                    ->orderByDesc('total_sold');
            } elseif ($sort === 'highest_price') {
                // harga tertinggi
                $query->orderBy('price', 'desc');
            } elseif ($sort === 'lowest_price') {
                // harga terendah
                $query->orderBy('price', 'asc');
            }
        });

        $query->when(!request()->filled('sort') && !request()->filled('sort_field'), function ($query) {
            $query->orderBy('updated_at', 'desc');
        });
    }
}
