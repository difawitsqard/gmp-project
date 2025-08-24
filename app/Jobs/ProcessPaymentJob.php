<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Support\Carbon;
use App\Jobs\SendPaymentEmailJob;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // retry dan backoff
    public $tries = 3;
    public $backoff = [30, 60, 120];

    protected $midtransUuid;
    protected $notification;

    /**
     * Create a new job instance.
     */
    public function __construct($midtransUuid, $notification)
    {
        $this->midtransUuid = $midtransUuid;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     */
    public function handle(MidtransService $midtransService)
    {
        // Generate ID unik untuk notifikasi
        $transactionId = $this->notification->transaction_id ?? null;
        $orderStatus = $this->notification->transaction_status ?? 'unknown';

        // Jika tidak ada transaction_id, gunakan kombinasi order_id dan status
        $notificationId = $transactionId
            ? "midtrans-{$transactionId}-{$orderStatus}"
            : "midtrans-{$this->midtransUuid}-{$orderStatus}";

        // Periksa apakah notifikasi ini sudah pernah diproses
        // Cache::add() hanya berhasil jika key belum ada
        $isFirstProcess = Cache::add($notificationId, true, 10080); // TTL 7 hari (menit)

        if (!$isFirstProcess) {
            Log::info("Notifikasi pembayaran sudah diproses: {$notificationId}, status: {$orderStatus}");
            return;
        }

        // Log untuk audit trail
        Log::info("Memproses notifikasi pembayaran", [
            'notification_id' => $notificationId,
            'order_id' => $this->midtransUuid,
            'status' => $orderStatus
        ]);

        // Proses pembayaran (kode yang sudah ada)
        DB::beginTransaction();
        try {
            $order = Order::whereHas('payments', function ($q) {
                $q->where('midtrans_uuid', $this->midtransUuid);
            })->lockForUpdate()->first();

            if (!$order) {
                Log::warning("Order not found for midtrans_uuid: {$this->midtransUuid}");
                DB::rollBack();
                return;
            }

            $lastPayment = $order->latestPayment;

            if ($lastPayment->midtrans_uuid !== $this->midtransUuid) {
                Log::info('Payment callback ignored: not the latest payment for order ' . $order->uuid);
                DB::rollBack();
                return;
            }

            $getStatus = $this->notification->transaction_status ?? 'unknown';
            $getPaymentType = $this->notification->payment_type ?? null;
            $getExpiryTime = $this->notification->expiry_time ? Carbon::parse($this->notification->expiry_time) : null;
            $getSettlementTime = $this->notification->settlement_time ? Carbon::parse($this->notification->settlement_time) : null;

            Log::info("Processing payment for order {$order->uuid}, status: {$getStatus}");

            switch ($getStatus) {
                case 'capture':
                case 'settlement':
                    $this->handleSuccessPayment($order, $lastPayment, $getPaymentType, $getSettlementTime, $midtransService);
                    break;

                case 'pending':
                    $this->handlePendingPayment($lastPayment, $getPaymentType, $getExpiryTime);
                    break;

                case 'expire':
                    $this->handleExpiredPayment($order, $lastPayment, $getPaymentType);
                    break;

                case 'cancel':
                    $this->handleCancelledPayment($order, $lastPayment, $getPaymentType);
                    break;

                case 'deny':
                case 'failure':
                    $this->handleFailedPayment($order, $lastPayment, $getPaymentType);
                    break;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            if ($this->attempts() >= $this->tries) {
                Log::error("Kegagalan final untuk {$notificationId}: " . $e->getMessage());
            } else {
                // Jika masih ada retry, hapus dari cache agar bisa dicoba lagi
                Cache::forget($notificationId);
                Log::warning("Gagal memproses {$notificationId}, akan dicoba lagi: " . $e->getMessage());
            }

            throw $e;
        }
    }

    private function handleSuccessPayment($order, $lastPayment, $paymentType, $settlementTime, $midtransService)
    {
        if ($order->status !== 'pending') {
            Log::info("Order {$order->uuid} status is not pending, skipping success handling");
            return;
        }

        $lastPayment->update([
            'payment_type' => $paymentType,
            'status' => 'paid',
            'paid_at' => $settlementTime,
        ]);

        // Try process stock reduction
        $stockReductionSuccess = $this->processStockReduction($order);

        if ($stockReductionSuccess) {
            $order->update(['status' => 'waiting_processing']);
            SendPaymentEmailJob::dispatch($order, 'success');
            $this->cancelOrdersWithOutOfStockProducts($order, $midtransService);
            Log::info("Payment successful for order {$order->uuid}");
        } else {
            // Stock reduction failed, cancel order
            $order->update(['status' => 'cancelled']);
            $lastPayment->update(['status' => 'cancelled']);
            SendPaymentEmailJob::dispatch($order, 'stock_failed');
            Log::warning("Order {$order->uuid} cancelled due to insufficient stock");
        }
    }

    private function handlePendingPayment($lastPayment, $paymentType, $expiryTime)
    {
        $lastPayment->update([
            'payment_type' => $paymentType,
            'status' => 'pending',
            'expired_at' => $expiryTime,
        ]);

        // Send pending email
        SendPaymentEmailJob::dispatch($lastPayment->order, 'pending');
    }

    private function handleExpiredPayment($order, $lastPayment, $paymentType)
    {
        $order->update(['status' => 'cancelled']);
        $lastPayment->update([
            'payment_type' => $paymentType,
            'status' => 'expired',
        ]);

        SendPaymentEmailJob::dispatch($order, 'expired');
    }

    private function handleCancelledPayment($order, $lastPayment, $paymentType)
    {
        $order->update(['status' => 'cancelled']);
        $lastPayment->update([
            'payment_type' => $paymentType,
            'status' => 'cancelled',
        ]);

        SendPaymentEmailJob::dispatch($order, 'cancelled');
    }

    private function handleFailedPayment($order, $lastPayment, $paymentType)
    {
        $order->update([
            'status' => 'cancelled',
            'note' => 'Pembayaran gagal',
        ]);
        $lastPayment->update([
            'payment_type' => $paymentType,
            'status' => 'failed',
        ]);

        SendPaymentEmailJob::dispatch($order, 'failed');
    }

    private function processStockReduction($order)
    {
        $batchReference = 'ORDER-' . $order->uuid;

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            // gunakan lock untuk memastikan stok tidak berubah saat proses pengurangan
            $lockedProduct = $product->newQuery()
                ->where('id', $product->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProduct) {
                Log::error("Product {$product->id} not found during stock reduction");
                return false;
            }

            // Check if stock is sufficient before attempting reduction
            if ($lockedProduct->qty < $item->quantity) {
                Log::error("Insufficient stock for product {$product->id} in order {$order->uuid}");
                return false;
            }

            try {
                $lockedProduct->decreaseStock(
                    $item->quantity,
                    $order,
                    "Pengurangan stok Order #{$order->uuid}",
                    $batchReference
                );
            } catch (\Exception $e) {
                Log::error("Stock reduction failed for product {$product->id} in order {$order->uuid}: " . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    private function cancelOrdersWithOutOfStockProducts($currentOrder, $midtransService)
    {
        foreach ($currentOrder->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            // gunakan lock untuk memastikan stok tidak berubah saat dicek
            $freshProduct = $product->newQuery()
                ->where('id', $product->id)
                ->lockForUpdate()
                ->first();

            if (!$freshProduct || $freshProduct->qty <= 0) {
                $otherOrders = Order::whereIn('status', ['pending', 'waiting_confirmation'])
                    ->where('id', '!=', $currentOrder->id)
                    ->whereHas('items', function ($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })
                    ->get();

                foreach ($otherOrders as $otherOrder) {
                    $otherOrder->update([
                        'status' => 'cancelled',
                        'note' => "'Beberapa produk habis pada pesanan ini, pesanan anda dibatalkan'"
                    ]);

                    if ($otherOrder->latestPayment) {
                        try {
                            $midtransService->cancelTransaction($otherOrder->latestPayment->midtrans_uuid);
                        } catch (\Exception $e) {
                            Log::warning("Failed to cancel Midtrans transaction for order {$otherOrder->uuid}: " . $e->getMessage());
                        }

                        $otherOrder->latestPayment->update(['status' => 'cancelled']);
                    }

                    SendPaymentEmailJob::dispatch($otherOrder, 'stock_cancelled');
                }
            }
        }
    }
}
