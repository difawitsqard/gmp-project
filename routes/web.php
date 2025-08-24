<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NixtlaTestController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\StockManagementController;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    if (auth()->check()) {
        // return Inertia::render('dashboard/admin-dashboard', [
        //     'canLogin' => Route::has('login'),
        //     'laravelVersion' => Application::VERSION,
        //     'phpVersion' => PHP_VERSION,
        // ]);
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserManagementController::class)
        ->only(['index', 'store', 'show', 'update']);

    Route::group(['middleware' => ['role:Staff Gudang|Manajer Stok']], function () {
        Route::get('/stock-transactions', [StockManagementController::class, 'stockTransactions'])
            ->name('stock.transactions');

        Route::resource('product-categories', ProductCategoryController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        Route::resource('units', UnitController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
    });

    Route::resource('/products/stock-management', StockManagementController::class)
        ->only(['index', 'store'])->middleware('role:Staff Gudang|Manajer Stok');

    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{id}/time-series-chart', [ProductController::class, 'timeSeriesChart'])->name('products.time-series-chart');
    Route::resource('products', ProductController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('taxes', TaxController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('/orders/{id}/process', [OrderController::class, 'process'])->name('orders.process');
    Route::post('/orders/{id}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::resource('orders', OrderController::class)
        ->only(['index', 'create', 'store', 'update']);
    Route::get('/orders/{uuid}', [OrderController::class, 'show'])->name('orders.show');

    Route::post('forecasting/request', [ForecastController::class, 'requestForecast'])->name('forecasting.request');
    Route::resource('forecasting', ForecastController::class)
        ->only(['index', 'create', 'show']);

    Route::post('/payments/{order}/retry', [PaymentController::class, 'retryPayment'])->name('payments.retry');
});

// nixtla test
Route::get('/nixtla-test', [NixtlaTestController::class, 'forecast'])
    ->name('nixtla.test.forecast');

Route::post('/midtrans/webhook', [PaymentController::class, 'midtransCallback'])
    ->name('midtrans.webhook');

Route::get('demo-openai', [ForecastController::class, 'openAIDemoTest'])->name('demo.openai');

Route::get('/test-email', function () {
    // Send a test email
    Mail::to('difawitsqard@gmail.com')->send(new \App\Mail\testmail());
    return response()->json([
        'message' => 'Test email sent successfully.',
    ])->setStatusCode(200);
})->name('test.email');

Route::get('/email-preview/{type?}', function ($type = 'success') {
    // Ambil order sample dari database
    $order = App\Models\Order::with(['items.product.category', 'latestPayment', 'taxes'])->first();

    $order = false;

    // Jika tidak ada order, buat data dummy
    if (!$order) {
        $order = (object) [
            'name' => 'John Doe',
            'uuid' => 'ORD123456789',
            'created_at' => now(),
            'shipping_method' => 'pickup',
            'sub_total' => 75000,
            'shipping_fee' => 12500,
            'total' => 87500,
            'items' => collect([
                (object) [
                    'quantity' => 2,
                    'price' => 25000,
                    'subtotal' => 50000,
                    'product' => (object) [
                        'name' => 'Produk Sample',
                        'category' => (object) ['name' => 'Kategori Sample']
                    ]
                ],
                (object) [
                    'quantity' => 1,
                    'price' => 25000,
                    'subtotal' => 25000,
                    'product' => (object) [
                        'name' => 'Produk Lainnya',
                        'category' => (object) ['name' => 'Kategori Lain']
                    ]
                ]
            ]),
            'latestPayment' => (object) [
                'paid_at' => now(),
                'expired_at' => now()->addMinutes(30),
                'payment_type' => 'qris',
                'status' => 'paid'
            ],
            'note' => 'Silakan hubungi kami jika ada pertanyaan.',
            'taxes' => collect()
        ];
    }

    return view('emails.payment-notification', [
        'order' => $order,
        'type' => $type
    ]);
})->name('email.preview');
