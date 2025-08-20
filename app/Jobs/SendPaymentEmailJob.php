<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentNotificationMail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPaymentEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, $type)
    {
        $this->order = $order;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::to($this->order->email)->send(
                new PaymentNotificationMail($this->order, $this->type)
            );
        } catch (\Exception $e) {
            Log::error("Failed to send payment email for order {$this->order->uuid}: " . $e->getMessage());
        }
    }
}
