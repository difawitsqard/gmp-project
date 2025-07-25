<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
  protected $user;

  public function __construct()
  {
    $this->user = auth()->user();
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(MidtransService $midtransService, Request $request)
  {
    if ($this->user->hasRole('Pelanggan')) {
      $request->merge(['customer_id' => $this->user->id]);

      // Jika pelanggan memilih delivery, status pesanan akan menjadi 'waiting_confirmation'
      if ($request->shipping_method === 'delivery') {
        $initialStatus = 'waiting_confirmation';
      } else {
        $initialStatus = 'pending';
      }
    } else {
      $initialStatus = 'pending'; // Admin langsung pending (bisa bayar)
    }

    // validasi customer_id
    $request->validate([
      'customer_id' => 'required|exists:users,id',
    ]);

    $customer = User::findOrFail($request->customer_id);

    // Merge Request
    $request->merge([
      'name' => $customer->name,
      'email' => $customer->email,
      'phone' => (string) $customer->phone,
      'address' => $customer->address,
    ]);

    $validated = $request->validate([
      'customer_id' => 'required|exists:users,id',
      'name' => 'required|string|max:255',
      'email' => 'required|email',
      'phone' => 'required|string|max:20',
      'address' => 'required|string|max:255',
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

    //dd($validated);

    DB::beginTransaction();
    try {

      $order = Order::create([
        'customer_id' => $customer->id,
        'name' => $customer->name,
        'email' => $customer->email,
        'phone' => $customer->phone,
        'address' => $customer->address,
        'shipping_method' => $validated['shipping_method'],
        'shipping_fee' => $validated['shipping_method'] == 'delivery' ? $validated['shipping_fee'] : null,
        'uplink_id' => $this->user->hasRole('Admin') ? $this->user->id : null,
        'status' => $initialStatus,
        'sub_total' => 0,
        'total' => 0,
      ]);

      $subTotal = 0;

      foreach ($validated['addedProducts'] as $product) {
        $price = Product::find($product['id'])->price;
        $quantity = $product['qty'];
        $subtotal = $price * $quantity;

        // periksa stok apakah cukup
        $productModel = Product::findOrFail($product['id']);
        if ($productModel->qty < $quantity) {
          DB::rollBack();
          $errorKey = 'addedProducts.' . array_search($product, $validated['addedProducts']) . '.qty';
          $errorMessage = 'Stok tidak cukup untuk produk ' . $productModel->name;
          if ($request->wantsJson()) {
            return response()->json([
              'status' => false,
              'code' => 422,
              'message' => $errorMessage,
              'errors' => [
                $errorKey => [$errorMessage]
              ]
            ], 422);
          }
          return redirect()->back()->withErrors([
            $errorKey => $errorMessage,
          ]);
        }

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

      // Jika status waiting_confirmation, jangan buat token midtrans dulu
      if ($initialStatus === 'waiting_confirmation') {
        // Tidak perlu buat token midtrans, akan dibuat setelah admin konfirmasi
        DB::commit();

        if ($request->wantsJson()) {
          return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Pesanan berhasil dibuat dan menunggu konfirmasi admin',
            'data' => [
              'id' => $order->id,
              'uuid' => $order->uuid,
              'name' => $order->name,
              'sub_total' => $order->sub_total,
              'total' => $order->total,
              'status' => $order->status,
              'items' => $order->items,
              'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ],
          ], 200);
        }

        return redirect()->route('orders.show', ['uuid' => $order->uuid])->with([
          'success' => 'Pesanan berhasil dibuat dan menunggu konfirmasi admin',
        ]);
      } else {

        // Buat Snap Token midtrans
        $snapToken = $midtransService->createSnapToken($order);

        // create payment
        $order->payments()->create([
          'order_id' => $order->id,
          'midtrans_uuid' => $order->uuid,
          'status' => 'pending',
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
              'uuid' => $order->uuid,
              'name' => $order->name,
              'sub_total' => $order->sub_total,
              'total' => $order->total,
              'status' => $order->status,
              'items' => $order->items,
              'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ],
          ], 200);
        }

        return redirect()->route('orders.show', ['uuid' => $order->uuid])->with([
          'success' => 'Order created successfully',
          'midtrans_show_snap' => true,
        ]);
      }
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

  public function confirm(Request $request, $id)
  {
    if (!$this->user || !$this->user->hasRole('Admin')) {
      return back()->withErrors(['error' => 'Hanya admin yang dapat mengkonfirmasi pesanan.']);
    }

    $request->validate([
      'shipping_fee' => 'required|integer|min:0',
    ]);

    $order =  Order::findOrFail($id);


    if ($order->status !== 'waiting_confirmation') {
      return back()->withErrors(['error' => 'Pesanan tidak memerlukan konfirmasi.']);
    }

    DB::beginTransaction();
    try {
      $order->shipping_fee = $request->shipping_fee;
      $order->status = 'pending';
      // Hitung ulang total jika perlu
      $order->total = $order->sub_total + $order->taxes->sum('pivot.amount') + $order->shipping_fee;
      $order->save();

      // Buat Snap Token Midtrans jika belum ada
      if (!$order->latestPayment) {
        $midtransService = app(\App\Services\MidtransService::class);
        $snapToken = $midtransService->createSnapToken($order);
        $order->payments()->create([
          'order_id' => $order->id,
          'midtrans_uuid' => $order->uuid,
          'status' => 'pending',
          'snap_token' => $snapToken,
        ]);
      }

      DB::commit();
      return back()->with('success', 'Pesanan berhasil dikonfirmasi dan siap dibayar.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->withErrors(['error' => 'Gagal konfirmasi pesanan: ' . $e->getMessage()]);
    }
  }
}
