<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Jobs\ProcessPaymentJob;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function midtransCallback(MidtransService $midtransService)
    {
        try {
            if ($midtransService->isSignatureKeyVerified()) {
                $notification = $midtransService->notification();
                $callbackMidtransUuid = $notification->order_id ?? null;

                // Dispatch job untuk memproses payment secara asynchronous
                ProcessPaymentJob::dispatch($callbackMidtransUuid, $notification);

                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi diterima dan sedang diproses',
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
