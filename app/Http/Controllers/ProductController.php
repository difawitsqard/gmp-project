<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Services\ImageUploadService;
use App\Http\Requests\ProductRequest;
use App\Services\TimeSeriesValidationService;

use Illuminate\Support\Carbon;

class ProductController extends Controller
{

    protected $imageUploadService;
    protected TimeSeriesValidationService $validationService;

    public function __construct(TimeSeriesValidationService $validationService)
    {
        $this->imageUploadService = new ImageUploadService();
        $this->validationService = $validationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $user = auth()->user();
        // $user->assignRole('Manajer Stok');

        $perPage = is_numeric($request->perPage) ? $request->perPage :  10;

        $productsCategories = ProductCategory::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $units = Unit::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::filter()
            ->sorting()
            ->with(['category', 'unit'])
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

        return redirect()->route('products.index')->with('success', "Produk {$product->name} ({$product->sku}) berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            return back()->withErrors([
                'error' => 'Product not found'
            ]);
        }

        $product->load('category', 'unit', 'images');

        if (request()->expectsJson()) {
            return response()->json([
                'product' => $product
            ]);
        }

        return inertia('inventory/product-details', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with(['category', 'unit', 'images'])->find($id);

        if (!$product) {
            return back()->withErrors([
                'error' => 'Product not found'
            ]);
        }

        $categories = \App\Models\ProductCategory::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $units = \App\Models\Unit::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        return inertia('inventory/edit-product', [
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'component' => 'edit-product',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {

        $validatedData = $request->validated();

        $product = Product::find($id);

        // Update product data
        $product->update($validatedData);

        // Handle delete_image jika ada
        if (isset($validatedData['delete_image']) && !empty($validatedData['delete_image'])) {
            foreach ($validatedData['delete_image'] as $imageId) {
                $image = \App\Models\ProductImage::find($imageId);
                if ($image) {
                    $this->imageUploadService->deleteImage($image->image_path);
                    $image->delete();
                }
            }
        }

        // Handle new images
        if (isset($validatedData['images']) && !empty($validatedData['images'])) {
            foreach ($validatedData['images'] as $image) {
                $imagePath = $this->imageUploadService->uploadImage($image, 'products');
                $product->images()->create([
                    'image_path' => $imagePath,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', "Produk {$product->name} ({$product->sku}) berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product =  Product::find($id);
        if ($product) {
            try {
                $deleted = $product->delete();

                if ($deleted) {
                    return redirect()->route('products.index')->with('success', "Produk {$product->name} ({$product->sku}) berhasil dihapus.");
                } else {
                    return back()->withErrors([
                        'error' => "Produk {$product->name} ({$product->sku}) gagal dihapus."
                    ]);
                }
            } catch (\Exception $e) {
                return back()->withErrors([
                    'error' => 'Produk tidak dapat dihapus karena masih terhubung dengan data lain.'
                ]);
            }
        }
        return back()->withErrors([
            'error' => 'Produk tidak ditemukan.'
        ]);
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('order_date_start');
        $endDate = $request->input('order_date_end');
        $frequency = $request->input('frequency', 'D'); // Tambah parameter frequency

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

            $query->withCount(['orderItems as filtered_order_items_count' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
            }]);

            $query->orderByDesc('filtered_order_items_count');
        } else {
            $query->with('orderItems');
            $query->withCount('orderItems');
            $query->orderByDesc('order_items_count');
        }

        $totalCount = $query->count();
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $products = $query->paginate($perPage);

        // Append parameters
        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }
        if ($startDate) {
            $products->appends(['order_date_start' => $startDate]);
        }
        if ($endDate) {
            $products->appends(['order_date_end' => $endDate]);
        }
        if ($frequency) {
            $products->appends(['frequency' => $frequency]);
        }
        $products->appends(['per_page' => $perPage]);

        // Transform koleksi dengan forecast eligibility
        $products->getCollection()->transform(function ($product) use ($offset, $startDate, $endDate, $frequency, $request) {
            static $rankCounter = 0;
            $rankCounter++;
            $rank = $offset + $rankCounter;

            $orderCount = ($startDate || $endDate)
                ? $product->filtered_order_items_count
                : $product->order_items_count;

            // Tambahkan forecast eligibility check
            $forecastEligibility = $this->checkForecastEligibility($product, $request);

            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name ?? null,
                'unit' => $product->unit->name ?? null,
                'image_url' => $product->image_url,
                'totalOrderItems' => $orderCount,
                'qty' => $product->qty,
                'price' => $product->price,
                'min_stock' => $product->min_stock,
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
                'forecast_eligibility' => $forecastEligibility, // Tambah ini
            ];
        });

        return response()->json($products);
    }

    /**
     * Check if product is eligible for forecasting
     */
    private function checkForecastEligibility($product, $request): array
    {
        $startDate = $request->input('order_date_start')
            ? Carbon::parse($request->input('order_date_start'))
            : Carbon::now()->subYear();

        $endDate = $request->input('order_date_end')
            ? Carbon::parse($request->input('order_date_end'))
            : Carbon::now();

        $frequency = $request->input('frequency', 'D');

        // Generate time series untuk validasi
        $controller = app(\App\Http\Controllers\ForecastController::class);
        $timeSeries = $controller->getTimeSeriesData(
            $product->id,
            $frequency,
            $startDate,
            $endDate
        );

        // Gunakan validation service
        return $this->validationService->checkForecastEligibility($timeSeries, $frequency, $endDate);
    }
}
