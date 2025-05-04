<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Services\MidtransService;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('items.product')->get();
        $orders->load('items.product.category', 'items.product.unit');

        $orders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'name' => $order->name,
                'sub_total' => $order->sub_total,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'items' => $order->items,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return inertia('sales/sales-list', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $products->load('category', 'unit');
        $productCategories = ProductCategory::all();
        $productCategories->load('products');

        $productCategories = $productCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'items' => $category->products->count(),
            ];
        });

        return inertia('sales/sales-pos', [
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
            'name' => 'nullable|string|max:255',
            'addedProducts' => 'required|array',
            'addedProducts.*.id' => 'required|exists:products,id',
            'addedProducts.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $order = \App\Models\Order::create([
                'name' => $validated['name'],
                'sub_total' => 0,
                'total' => 0,
                'payment_method' => 'cash',
                'status' => 'pending',
                'payment_status' => 'unpaid',
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

            $order->update([
                'sub_total' => $subTotal,
                'total' => $subTotal, // Adjust if there are additional charges or discounts
            ]);

            $snapToken = $midtransService->createSnapToken($order);

            return response()->json([
                'message' => 'Order created successfully',
                'snap_token' => $snapToken,
                'midtrans_client_key' => config('services.midtrans.client_key'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
