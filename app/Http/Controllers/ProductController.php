<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Services\ImageUploadService;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{

    protected $imageUploadService;


    public function __construct()
    {
        $this->imageUploadService = new ImageUploadService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = is_numeric($request->perPage) ? $request->perPage :  10;

        $productsCategories = ProductCategory::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $units = Unit::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::filter()
            ->with(['category', 'unit'])
            ->orderBy('name', 'asc')
            ->paginate($perPage);
        $products->appends(['search' => $request->search]);

        if (!empty($request->search) && $request->search != '')
            $products->appends(['search' => $request->search]);

        $products->appends(['perPage' => $perPage]);

        return inertia('inventory/product-list/product-list', [
            'units' => $units,
            'productsCategories' => $productsCategories,
            'products' => $products,
            'filters' => $request->only('search', 'page', 'per_page'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\ProductCategory::all();
        $units = \App\Models\Unit::all();
        return inertia('inventory/add-product', [
            'categories' => $categories,
            'units' => $units,
            'component' => 'add-product',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {

        $validatedData = $request->validated();
        $product = Product::create($validatedData);

        foreach ($request->images as $image) {
            $imagePath = $this->imageUploadService->uploadImage($image, 'products');
            $product->images()->create([
                'image_path' => $imagePath,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        $product->load('category', 'unit');

        if ($product) {
            return inertia('inventory/product-details', [
                'product' => $product,
            ]);
        }
        return back()->withErrors([
            'error' => 'Product not found'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product =  Product::find($id);
        if ($product) {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        }
        return back()->withErrors([
            'error' => 'Product not found'
        ]);
    }


    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('order_date_start');
        $endDate = $request->input('order_date_end');

        // Inisialisasi query dasar
        $query = Product::filter()
            ->with(['category', 'unit', 'images']);

        // Load orderItems dengan kondisi tanggal jika parameter tersedia
        if ($startDate || $endDate) {
            $query->with(['orderItems' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
            }]);

            // Tambahkan withCount untuk filtered orderItems
            $query->withCount(['orderItems as filtered_order_items_count' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
            }]);

            // Urut berdasarkan jumlah order items dalam rentang tanggal
            $query->orderByDesc('filtered_order_items_count');
        } else {
            // Load orderItems tanpa filter jika tidak ada parameter tanggal
            $query->with('orderItems');
            $query->withCount('orderItems');
            $query->orderByDesc('order_items_count');
        }

        // Hitung total dan paginasi (kode yang sudah ada)
        $totalCount = $query->count();
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $products = $query->paginate($perPage);

        // Tambahkan parameter ke link paginasi (kode yang sudah ada, dengan penambahan)
        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }
        if ($startDate) {
            $products->appends(['order_date_start' => $startDate]);
        }
        if ($endDate) {
            $products->appends(['order_date_end' => $endDate]);
        }
        $products->appends(['per_page' => $perPage]);

        // Transform koleksi paginasi
        $products->getCollection()->transform(function ($product) use ($offset, $startDate, $endDate) {
            static $rankCounter = 0;
            $rankCounter++;
            $rank = $offset + $rankCounter;
            $image = $product->images->first();

            // Gunakan jumlah order yang sudah difilter jika ada parameter tanggal
            $orderCount = ($startDate || $endDate)
                ? $product->filtered_order_items_count
                : $product->order_items_count;

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name ?? null,
                'unit' => $product->unit->name ?? null,
                'image_url' => $image ? $image->image_url : null,
                'totalOrderItems' => $orderCount,
                'popularityRank' => $rank,
                'timeSeriesOrderItems' => [
                    'day' => $product->orderItems->groupBy(function ($orderItem) {
                        return $orderItem->created_at->format('Y-m-d');
                    })->count(),
                    'week' => $product->orderItems->groupBy(function ($orderItem) {
                        return $orderItem->created_at->copy()->startOfWeek()->format('Y-m-d');
                    })->count(),
                    'month' => $product->orderItems->groupBy(function ($orderItem) {
                        return $orderItem->created_at->copy()->startOfMonth()->format('Y-m-d');
                    })->count(),
                ],
            ];
        });

        return response()->json($products);
    }
}
