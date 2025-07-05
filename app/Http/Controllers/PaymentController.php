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

                // Log informasi status pembayaran
                Log::info('Midtrans Callback Expire: ' . $getExpiryTime);

                if ($getStatus == 'success') {
                    $order->update([
                        'status' => 'waiting_processing',
                    ]);
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
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
                    // lakukan sesuatu jika pembayaran expired, seperti mengirim notifikasi ke customer
                    // bahwa pembayaran expired dan harap melakukan pembayaran ulang
                    $lastPayment->update([
                        'payment_type' => $getPaymentType,
                        'status' => 'expired',
                    ]);
                }

                if ($getStatus == 'cancel') {
                    // lakukan sesuatu jika pembayaran dibatalkan
                }

                if ($getStatus == 'failed') {
                    // lakukan sesuatu jika pembayaran gagal
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
            $newOrderId =  $order->id . '-' . now()->timestamp;

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
            $midtransService->cancelTransaction($order->latestPayment->midtrans_order_id);

            // Buat Snap Token berdasarkan order_id unik tersebut
            $snapToken = $midtransService->createSnapToken($order, $newOrderId);

            // Simpan entri payment baru
            $order->payments()->create([
                'order_id' => $order->id,
                'snap_token' => $snapToken,
                'midtrans_order_id' => $newOrderId,
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
