<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Tax;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = is_numeric($request->per_page) ? $request->per_page :  10;

        $orders = Order::filter()
            ->with(['items.product', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'name' => $order->name,
                'sub_total' => $order->sub_total,
                'total' => $order->total,
                'status' => $order->status,
                'payment_status' => $order->latestPayment ? $order->latestPayment->status : null,
                'created_at' => $order->created_at,
            ];
        });

        $orders->appends(['search' => $request->search]);
        $orders->appends(['per_page' => $perPage]);

        if (!empty($request->search) && $request->search != '') {
            $orders->appends(['search' => $request->search]);
        }

        return inertia('sales/sales-list', [
            'orders' => $orders,
            'filters' => $request->only('search', 'page', 'per_page'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $perPage = is_numeric($request->per_page) ? $request->per_page :  12;

        $products = Product::filter()
            ->with(['category', 'unit'])
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        $productCategories = ProductCategory::withCount('products')->get();
        $productCategories = $productCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'items' => $category->products_count,
            ];
        });

        // taxes
        $taxes = Tax::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        return inertia('sales/sales-pos', [
            'taxes' => $taxes,
            'products' => $products,
            'productCategories' => $productCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MidtransService $midtransService, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shipping_method' => 'required|in:pickup,delivery',
            'shipping_fee' => [
                'required_if:shipping_method,delivery',
                'nullable',
                'integer',
                'min:0'
            ],
            'addedProducts' => 'required|array',
            'addedProducts.*.id' => 'required|exists:products,id',
            'addedProducts.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'name' => $validated['name'],
                'shipping_method' => $validated['shipping_method'],
                'shipping_fee' => $validated['shipping_method'] == 'delivery' ? $validated['shipping_fee'] : null,
                'sub_total' => 0,
                'total' => 0,
            ]);

            $subTotal = 0;

            foreach ($validated['addedProducts'] as $product) {
                $price = Product::find($product['id'])->price;
                $quantity = $product['qty'];
                $subtotal = $price * $quantity;

                $order->items()->create([
                    'product_id' => $product['id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'note' => null,
                ]);

                $subTotal += $subtotal;
            }

            // hitung pajak jika ada
            $totalTax = 0;
            $taxes = Tax::where('status', 1)->get();
            if ($taxes->count() > 0) {
                foreach ($taxes as $tax) {
                    $amount = $tax->percent
                        ? ($subTotal * $tax->percent) / 100
                        : $tax->fixed_amount;
                    $order->taxes()->attach($tax->id, ['amount' => $amount]);
                    $totalTax += $amount;
                }
            }

            $total = $subTotal + $totalTax + ($validated['shipping_method'] == 'delivery' ? $validated['shipping_fee'] : 0);

            $order->update([
                'sub_total' => $subTotal,
                'total' =>  $total,
            ]);

            // Buat Snap Token midtrans
            $snapToken = $midtransService->createSnapToken($order, 'ord2-' . $order->id);

            // create payment
            $order->payments()->create([
                'order_id' => $order->id,
                'status' => 'pending',
                'midtrans_order_id' => 'ord2-' . $order->id,
                'snap_token' => $snapToken,
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'code' => 200,
                    'message' => 'Order created successfully',
                    'data' => [
                        'id' => $order->id,
                        'name' => $order->name,
                        'sub_total' => $order->sub_total,
                        'total' => $order->total,
                        'status' => $order->status,
                        'items' => $order->items,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    ],
                ], 200);
            }

            return redirect()->route('orders.show', ['order' => $order->id])->with([
                'success' => 'Order created successfully',
                'midtrans_show_snap' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'code' => 500,
                    'message' => 'Failed to create order',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors([
                'error' => 'Failed to create order: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        $order->load('items.product.category', 'items.product.unit', 'latestPayment', 'taxes');

        //$midtransService = new MidtransService();

        $midtrans_show_snap = session('midtrans_show_snap');

        return inertia('sales/sales-detail', [
            'order' => $order,
            'midtrans_show_snap' => $midtrans_show_snap ?? false,
            'midtrans_snap_token' => $order->latestPayment->status == 'pending' ? $order->latestPayment->snap_token : null,
            'midtrans_client_key' => config('midtrans.client_key'),
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
        //
    }

    public function cancel(MidtransService $midtransService, string $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->load('items.product.category', 'items.product.unit', 'latestPayment', 'taxes');

            if ($order->latestPayment) {
                $midtransService->cancelTransaction($order->latestPayment->midtrans_order_id);
                $order->latestPayment->update(['status' => 'cancelled']);
            }

            $order->update(['status' => 'cancelled']);

            return back()->with([
                'order' =>  $order,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to cancel order: ' . $e->getMessage(),
            ]);
        }
    }
}
