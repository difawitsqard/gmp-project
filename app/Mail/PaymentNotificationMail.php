<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class PaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;
    protected $type;

    public function __construct(Order $order, $type)
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->getSubject();

        return $this->view('emails.payment-notification')
            ->subject($subject)
            ->with([
                'order' => $this->order,
                'type' => $this->type,
            ]);
    }

    private function getSubject()
    {
        switch ($this->type) {
            case 'success':
                return "Pembayaran Berhasil - Order #{$this->order->uuid}";
            case 'pending':
                return "Menunggu Pembayaran - Order #{$this->order->uuid}";
            case 'expired':
                return "Pembayaran Kadaluarsa - Order #{$this->order->uuid}";
            case 'cancelled':
                return "Pesanan Dibatalkan - Order #{$this->order->uuid}";
            case 'failed':
                return "Pembayaran Gagal - Order #{$this->order->uuid}";
            case 'stock_failed':
                return "Pesanan Dibatalkan - Stok Tidak Cukup - Order #{$this->order->uuid}";
            case 'stock_cancelled':
                return "Pesanan Dibatalkan - Stok Habis - Order #{$this->order->uuid}";
            default:
                return "Notifikasi Pesanan - Order #{$this->order->uuid}";
        }
    }
}
