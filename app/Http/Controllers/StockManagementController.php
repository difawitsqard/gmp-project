<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\stockTransaction;
use Illuminate\Support\Facades\DB;

class StockManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productsCategories = ProductCategory::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $units = Unit::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $stockTransactions = stockTransaction::filter()
            ->sorting()
            ->with(['product', 'product.unit', 'createdBy'])
            ->paginate(10);

        return inertia(
            'inventory/stock-management',
            [
                'units' => $units,
                'productsCategories' => $productsCategories,
                'component' => 'stock-management',
                'stockTransactions' => $stockTransactions,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi request
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.type' => 'required|in:in,out',
            'products.*.note' => 'nullable|string',

        ]);

        // Inisialisasi koleksi untuk menyimpan produk yang ditemukan
        $productsToUpdate = [];
        $errors = [];

        // 1. Preload semua produk untuk pemeriksaan stok
        $productIds = array_column($validated['products'], 'id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // 2. Validasi semua produk terlebih dahulu
        foreach ($validated['products'] as $index => $productData) {
            $product = $products->get($productData['id']);

            if (!$product) {
                $errors[] = "Produk dengan ID {$productData['id']} tidak ditemukan.";
                continue;
            }

            // Jika tipe adalah pengurangan, pastikan stok mencukupi
            if ($productData['type'] === 'out' && $product->qty < $productData['qty']) {
                $errors[] = "Stok tidak cukup untuk produk {$product->name}. Stok tersedia: {$product->qty}.";
                continue;
            }

            // Tambahkan ke koleksi jika valid
            $productsToUpdate[] = [
                'product' => $product,
                'qty' => $productData['qty'],
                'type' => $productData['type'],
                'note' => $productData['note'] ?? null
            ];
        }

        // 3. Jika ada error, kembalikan error dan batalkan transaksi
        if (count($errors) > 0) {
            return back()->withErrors([
                'message' => 'Terdapat error dalam penyesuaian stok:',
                'errors' => $errors
            ]);
        }

        // 4. Mulai transaksi database
        DB::beginTransaction();
        try {
            // Buat batch reference unik untuk mengelompokkan transaksi terkait
            $batchReference = 'ADJ-' . date('YmdHis') . '-' . auth()->id();

            // 5. Proses setiap produk
            foreach ($productsToUpdate as $data) {
                $product = $data['product'];
                $qty = $data['qty'];
                $type = $data['type'];
                $note = $data['note'] ?? null;

                // Perbarui stok produk dan buat transaksi menggunakan helper methods
                if ($type === 'in') {
                    // Buat "dummy object" untuk menyimpan batch_reference
                    $sourceData = new \stdClass();
                    $sourceData->batch_reference = $batchReference;

                    // Panggil increaseStock dengan parameter tambahan
                    $product->increaseStock($qty, null, $note, $batchReference);
                } else {
                    // Panggil decreaseStock dengan parameter tambahan
                    $product->decreaseStock($qty, null, $note, $batchReference);
                }
            }

            // 6. Commit transaksi jika berhasil
            DB::commit();

            return redirect()->route('products.index')->with('success', 'Penyesuaian stok berhasil disimpan.');
        } catch (\Exception $e) {
            // 7. Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Log error untuk debugging
            \Log::error('Stock adjustment error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()->withErrors([
                'message' => 'Terjadi kesalahan saat menyimpan penyesuaian stok.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function stockTransactions(Request $request)
    {
        $productId = $request->input('product_id');

        $query = stockTransaction::filter()
            ->sorting()
            ->with(['product', 'product.unit', 'createdBy']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $stockTransactions = $query->paginate(5);

        return response()->json($stockTransactions);
    }
}
