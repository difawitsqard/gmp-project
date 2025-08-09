<?php

namespace App\Services;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Midtrans\Transaction;
use Midtrans\Notification;

class MidtransService
{
    protected string $serverKey;
    protected string $isProduction;
    protected string $isSanitized;
    protected string $is3ds;

    /**
     * MidtransService constructor.
     *
     * Menyiapkan konfigurasi Midtrans berdasarkan pengaturan yang ada di file konfigurasi.
     */
    public function __construct()
    {
        // Konfigurasi server key, environment, dan lainnya
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production');
        $this->isSanitized = config('midtrans.is_sanitized');
        $this->is3ds = config('midtrans.is_3ds');

        // Mengatur konfigurasi global Midtrans
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }

    /**
     * Membuat snap token untuk transaksi berdasarkan data order.
     *
     * @param Order $order Objek order yang berisi informasi transaksi.
     *
     * @return string Snap token yang dapat digunakan di front-end untuk proses pembayaran.
     * @throws Exception Jika terjadi kesalahan saat menghasilkan snap token.
     */
    public function createSnapToken(Order $order, $costumOrderId = null): string
    {
        // Jika uuid tidak diberikan, gunakan nomor order yang ada
        $orderNumber = $costumOrderId ?? $order->uuid;

        // data transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => $order->total,
            ],
            'item_details' => $this->mapItemsToDetails($order),
            'customer_details' => $this->getCustomerDetails($order),
            "custom_field1" => 'This is for demo purposes only',
        ];

        $params['customer_details']['shipping_address'] = $this->getCustomerDetails($order);

        try {
            // Membuat snap token
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            // Menangani error jika gagal mendapatkan snap token
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Memvalidasi apakah signature key yang diterima dari Midtrans sesuai dengan signature key yang dihitung di server.
     *
     * @return bool Status apakah signature key valid atau tidak.
     */
    public function isSignatureKeyVerified(): bool
    {
        $notification = $this->notification();

        // Membuat signature key lokal dari data notifikasi
        $localSignatureKey = hash(
            'sha512',
            $notification->order_id . $notification->status_code .
                $notification->gross_amount . $this->serverKey
        );

        // dd($notification);

        // Memeriksa apakah signature key valid
        return $localSignatureKey === $notification->signature_key;
    }

    /**
     * Mendapatkan data order berdasarkan order_id yang ada di notifikasi Midtrans.
     *
     * @return Order Objek order yang sesuai dengan order_id yang diterima.
     */
    public function getOrder(): Order
    {
        // get request payload
        //$payload = file_get_contents('php://input');
        //Log::info('Midtrans Payload', [$payload]);

        $notification = $this->notification(); // Ambil data dari Midtrans payload

        $payment = Payment::with('order',  'order.items')
            ->where('midtrans_uuid', $notification->order_id)
            ->firstOrFail();

        return $payment->order;
    }

    /**
     * Mendapatkan notifikasi Midtrans sebagai objek.
     * Jika payment_type adalah 'dana', kembalikan sebagai objek dari array payload.
     * Jika tidak, gunakan Notification bawaan Midtrans.
     *
     * @return object
     */
    public function notification()
    {
        $raw_notification = json_decode(file_get_contents('php://input'), true);
        $payment_type = $raw_notification['payment_type'] ?? null;

        if ($payment_type === 'dana') {
            // Kembalikan sebagai object stdClass agar bisa diakses seperti $obj->nama
            return json_decode(json_encode($raw_notification));
        } else {
            // Kembalikan Notification Midtrans (sudah berbentuk object)
            return new Notification();
        }
    }

    /**
     * Mendapatkan status transaksi berdasarkan status yang diterima dari notifikasi Midtrans.
     *
     * @return string Status transaksi ('success', 'pending', 'expire', 'cancel', 'failed').
     */
    public function getStatus(): string
    {
        $notification = new Notification();
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;

        return match ($transactionStatus) {
            'capture' => ($fraudStatus == 'accept') ? 'success' : 'pending',
            'settlement' => 'success',
            'deny' => 'failed',
            'cancel' => 'cancel',
            'expire' => 'expire',
            'pending' => 'pending',
            default => 'unknown',
        };
    }

    /**
     * Mendapatkan waktu kedaluwarsa dari notifikasi Midtrans.
     *
     * @return string Waktu kedaluwarsa dalam format ISO 8601.
     */
    public function getExpiryTime(): string
    {
        $notification = new Notification();

        // Mengambil waktu kedaluwarsa dari notifikasi
        $expiryTime = $notification->expiry_time;

        // Pastikan waktu kedaluwarsa dalam format ISO 8601
        return date('c', strtotime($expiryTime));
    }

    /**
     * Mendapatkan jenis pembayaran dari notifikasi Midtrans.
     *
     * @return string Jenis pembayaran ('bank_transfer', 'credit_card', dll).
     */
    public function getPaymentType(): string
    {
        $notification = new Notification();

        // Mengambil jenis pembayaran dari notifikasi
        return $notification->payment_type;
    }

    /**
     * Mendapatkan status transaksi berdasarkan order_id.
     *
     * @param string $orderId ID order yang ingin diperiksa statusnya.
     * @return string Status transaksi ('success', 'pending', 'expire', 'cancel', 'failed').
     */
    public function getStatusByOrderId(string $orderId): string
    {
        try {
            $status = Transaction::status($orderId);
        } catch (Exception $e) {
            // Jika order belum ada atau gagal mengambil status, kembalikan 'not_found'
            return 'not_found';
        }

        $transactionStatus = $status->transaction_status ?? null;
        $fraudStatus = $status->fraud_status ?? null;

        if (!$transactionStatus) {
            return 'not_found';
        }

        return match ($transactionStatus) {
            'capture' => ($fraudStatus == 'accept') ? 'success' : 'pending',
            'settlement' => 'success',
            'deny' => 'failed',
            'cancel' => 'cancel',
            'expire' => 'expire',
            'pending' => 'pending',
            default => 'unknown',
        };
    }

    public function getTransactionByOrderId(string $orderId)
    {
        try {
            return Transaction::status($orderId);
        } catch (Exception $e) {
            // Jika order belum ada atau gagal mengambil status, kembalikan null
            return null;
        }
    }

    /**
     * Memetakan item dalam order menjadi format yang dibutuhkan oleh Midtrans.
     *
     * @param Order $order Objek order yang berisi daftar item.
     * @return array Daftar item yang dipetakan dalam format yang sesuai.
     */
    protected function mapItemsToDetails(Order $order): array
    {
        // Pastikan relasi sudah dimuat untuk menghindari N+1 problem
        $order->loadMissing(['items.product', 'taxes']);

        // produk
        $items = $order->items->map(function ($item) {
            return [
                'id' => $item->id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name ?? 'Product',
            ];
        })->toArray();

        // pengiriman
        $items[] = [
            'id' => 'shipping',
            'price' => $order->shipping_fee ?? 0,
            'quantity' => 1,
            'name' => 'Shipping Cost',
        ];

        // pajak
        foreach ($order->taxes as $tax) {
            $taxName = $tax->name;
            if (!empty($tax->percent)) {
                $taxName .= ' (' . $tax->percent . '%)';
            }
            $items[] = [
                'id' => 'tax_' . $tax->id,
                'price' => $tax->pivot->amount ?? 0,
                'quantity' => 1,
                'name' => $taxName,
            ];
        }

        return $items;
    }

    /**
     * Mendapatkan informasi customer dari order.
     *
     * @param Order $order Objek order yang berisi informasi tentang customer.
     * @return array Data customer yang akan dikirim ke Midtrans.
     */
    protected function getCustomerDetails(Order $order): array
    {
        // Sesuaikan data customer dengan informasi yang dimiliki oleh aplikasi Anda
        return [
            'first_name' => $order->name,
            'email' => $order->email,
            'phone' => $order->phone,
            'address' => $order->address,
        ];
    }

    /**
     * Membatalkan transaksi Midtrans berdasarkan order_id.
     *
     * @param string $orderId
     * @return mixed Response dari Midtrans atau null jika gagal.
     */
    public function cancelTransaction(string $orderId)
    {
        try {
            return Transaction::cancel($orderId);
        } catch (\Exception $e) {
            // Log error jika perlu
            Log::error('Midtrans cancel error: ' . $e->getMessage());
            return null;
        }
    }
}
