<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Services\MidtransService;

class PaymentController extends Controller
{
    public function midtransCallback(MidtransService $midtransService)
    {
        try {
            if ($midtransService->isSignatureKeyVerified()) {
                $order = $midtransService->getOrder();
                $lastPayment = $order->latestPayment;

                $getStatus = $midtransService->getStatus();
                $getPaymentType = $midtransService->getPaymentType();
                $getExpiryTime = $midtransService->getExpiryTime();

                $notification = $midtransService->notification();
                $callbackMidtransUuid = $notification->order_id ?? null;

                // Log informasi status pembayaran
                // Log::info('Midtrans Callback Expire: ' . $getExpiryTime);

                // Abaikan callback jika order_id tidak sesuai dengan yang terakhir
                if ($lastPayment->midtrans_uuid !== $callbackMidtransUuid) {
                    // Log::info('Midtrans Callback ignored: payment is not the latest for this order.');
                    return response()->json([
                        'success' => true,
                        'message' => 'Callback ignored: payment is not the latest for this order.',
                    ]);
                }

                if ($getStatus == 'success' && $order->status == 'pending') {
                    $order->update([
                        'status' => 'waiting_processing',
                    ]);
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);


                    // Buat batch reference unik untuk transaksi stok
                    $batchReference = 'ORDER-' . $order->uuid;

                    // Kurangi stok produk menggunakan decreaseStock seperti di StockManagementController
                    foreach ($order->items as $item) {
                        $product = $item->product;


                        if ($product) {
                            try {
                                $product->decreaseStock(
                                    $item->quantity,
                                    $order, // source: order
                                    "Pengurangan stok Order #{$order->uuid}",
                                    $batchReference // batch_reference
                                );
                            } catch (\Exception $e) {
                                // Jika stok tidak cukup, batalkan pesanan ini
                                $order->update(['status' => 'cancelled']);
                                $lastPayment->update(['status' => 'cancelled']);
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Stok produk tidak cukup, pesanan dibatalkan.',
                                ]);
                            }
                        }
                    }

                    // Batalkan pesanan lain yang belum diproses jika stok produk sudah habis
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        if ($product && $product->quantity <= 0) {
                            $otherOrders = Order::whereIn('status', ['pending', 'waiting_confirmation'])
                                ->whereHas('items', function ($q) use ($product) {
                                    $q->where('product_id', $product->id);
                                })
                                ->get();

                            foreach ($otherOrders as $otherOrder) {
                                $otherOrder->update([
                                    'status' => 'cancelled',
                                    'note'   => 'Stok produk habis, pesanan dibatalkan'
                                ]);
                                if ($otherOrder->latestPayment) {
                                    $otherOrder->latestPayment->update(['status' => 'cancelled']);
                                }
                            }
                        }
                    }
                }

                if ($getStatus == 'pending') {
                    // lakukan sesuatu jika pembayaran masih pending, seperti mengirim notifikasi ke customer
                    // bahwa pembayaran masih pending dan harap selesai pembayarannya
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'pending',
                        'expired_at' => $getExpiryTime,
                    ]);
                }

                if ($getStatus == 'expire') {
                    $order->update([
                        'status' => 'cancelled',
                    ]);
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'expired',
                    ]);
                }

                if ($getStatus == 'cancel') {

                    $order->update([
                        'status' => 'cancelled',
                    ]);
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'cancelled',
                    ]);
                }

                if ($getStatus == 'failed') {
                    $order->update([
                        'status' => 'cancelled',
                        'note' => 'Pembayaran gagal',
                    ]);
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'failed',
                    ]);
                }

                return response()
                    ->json([
                        'success' => true,
                        'message' => 'Notifikasi berhasil diproses',
                    ]);
            } else {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function retryPayment(Order $order, MidtransService $midtransService)
    {
        try {
            // Buat order_id baru untuk keperluan Midtrans (harus unik)
            $newUUID =  $order->uuid . '-' . now()->timestamp;

            if (!$order->canbe_change_payment_method) {
                return response()->json([
                    'message' => 'Order cannot be changed',
                ], 400);
            }

            // cancel old payment
            $order->latestPayment->update([
                'status' => 'cancelled',
            ]);

            // cancel transaction di Midtrans
            $midtransService->cancelTransaction($order->latestPayment->midtrans_uuid);

            // Buat Snap Token berdasarkan order_id unik tersebut
            $snapToken = $midtransService->createSnapToken($order, $newUUID);

            // Simpan entri payment baru
            $order->payments()->create([
                'order_id' => $order->id,
                'midtrans_uuid' => $newUUID,
                'snap_token' => $snapToken,
                'status' => 'pending',
            ]);

            // return response()->json(['snap_token' => $snapToken]);

            return back()->with([
                'midtrans_snap_token' => $snapToken,
                'midtrans_show_snap' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Retry Payment Error: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Failed to retry payment: ' . $e->getMessage(),
            ]);
        }
    }
}
