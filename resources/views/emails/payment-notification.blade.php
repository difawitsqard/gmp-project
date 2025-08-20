<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifikasi Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Montserrat", sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
        }

        .email-container {
            max-width: 640px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(90deg, #0392de 0%, #da251e 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 32px 24px;
        }

        .status-card {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            border-left: 4px solid;
        }

        .status-success {
            background-color: #f0f9ff;
            border-color: #10b981;
        }

        .status-pending {
            background-color: #fefce8;
            border-color: #f59e0b;
        }

        .status-failed {
            background-color: #fef2f2;
            border-color: #ef4444;
        }

        .status-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .status-description {
            font-size: 14px;
            color: #6b7280;
        }

        .order-summary {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }

        .order-summary h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .order-info {
            gap: 12px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }

        .info-label {
            font-size: 14px;
            color: #6b7280;
        }

        .info-value {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .products-table th {
            background-color: #f3f4f6;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .products-table td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }

        .products-table tr:last-child td {
            border-bottom: none;
        }

        .products-table .text-center {
            text-align: center;
        }

        .products-table .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: 500;
            color: #374151;
        }

        .price-breakdown {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }

        .price-breakdown h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .price-item:last-child {
            border-bottom: none;
        }

        .price-item.total {
            border-bottom: none;
            padding-top: 16px;
            padding-bottom: 8px;
            font-weight: 700;
            font-size: 16px;
            color: #374151;
            margin-left: -20px;
            margin-right: -20px;
            padding-left: 20px;
            padding-right: 20px;
            border-radius: 6px;
        }

        .price-label {
            color: #6b7280;
            font-weight: 500;
            flex: 1;
            text-align: left;
        }

        .price-value {
            font-weight: 600;
            color: #374151;
            text-align: right;
            white-space: nowrap;
            margin-left: 16px;
        }

        .price-item.total .price-label {
            color: #374151;
            font-weight: 700;
        }

        .price-item.total .price-value {
            color: #374151;
            font-weight: 700;
            font-size: 18px;
        }

        .footer-message {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .footer-message p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .footer-message p:last-child {
            margin-bottom: 0;
        }

        .footer {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .footer p:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 640px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }

            .content {
                padding: 24px 16px;
            }

            .header {
                padding: 24px 16px;
            }

            .order-info {
                grid-template-columns: 1fr;
            }

            .products-table {
                font-size: 12px;
            }

            .products-table th,
            .products-table td {
                padding: 8px 4px;
            }

            .price-item {
                font-size: 13px;
                padding: 8px 0;
            }

            .price-item.total {
                font-size: 15px;
                margin-left: -16px;
                margin-right: -16px;
                padding-left: 16px;
                padding-right: 16px;
            }

            .price-item.total .price-value {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Notifikasi Pesanan</h1>
            <p>Terima kasih telah berbelanja dengan kami</p>
        </div>

        <div class="content">
            <p style="font-size: 16px; margin-bottom: 24px;">Halo <strong>{{ $order->name }}</strong>,</p>

            @if ($type == 'success')
                <div class="status-card status-success">
                    <div class="status-title">
                        Pembayaran Berhasil!
                    </div>
                    <div class="status-description">
                        Terima kasih telah melakukan pembayaran. Pesanan Anda sedang diproses dan akan segera disiapkan.
                    </div>
                </div>
            @elseif($type == 'pending')
                <div class="status-card status-pending">
                    <div class="status-title">
                        Menunggu Pembayaran
                    </div>
                    <div class="status-description">
                        Pesanan Anda telah dibuat. Silakan selesaikan pembayaran sebelum batas waktu yang ditentukan.
                    </div>
                </div>
            @elseif($type == 'expired')
                <div class="status-card status-failed">
                    <div class="status-title">
                        Pembayaran Kadaluarsa
                    </div>
                    <div class="status-description">
                        Batas waktu pembayaran telah habis. Pesanan Anda telah dibatalkan secara otomatis.
                    </div>
                </div>
            @elseif($type == 'cancelled')
                <div class="status-card status-failed">
                    <div class="status-title">
                        Pesanan Dibatalkan
                    </div>
                    <div class="status-description">
                        Pesanan Anda telah dibatalkan. Jika ada pertanyaan, silakan hubungi customer service kami.
                    </div>
                </div>
            @elseif($type == 'failed')
                <div class="status-card status-failed">
                    <div class="status-title">
                        Pembayaran Gagal
                    </div>
                    <div class="status-description">
                        Pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi customer service untuk
                        bantuan.
                    </div>
                </div>
            @elseif($type == 'stock_failed')
                <div class="status-card status-failed">
                    <div class="status-title">
                        Stok Tidak Cukup
                    </div>
                    <div class="status-description">
                        Maaf, stok produk tidak mencukupi saat memproses pesanan Anda. Pesanan telah dibatalkan dan dana
                        akan dikembalikan.
                    </div>
                </div>
            @elseif($type == 'stock_cancelled')
                <div class="status-card status-failed">
                    <div class="status-title">
                        Stok Habis
                    </div>
                    <div class="status-description">
                        Maaf, stok produk telah habis sehingga pesanan Anda harus dibatalkan. Kami mohon maaf atas
                        ketidaknyamanan ini.
                    </div>
                </div>
            @elseif($type == 'processed')
                <div class="status-card status-success">
                    <div class="status-title">
                        Pesanan Telah Diproses!
                    </div>
                    <div class="status-description">
                        Pesanan Anda telah selesai diproses oleh tim kami.
                        @if ($order->shipping_method == 'pickup')
                            Pesanan Anda siap untuk diambil di gudang kami.
                        @else
                            Pesanan Anda akan segera dikirimkan ke alamat tujuan.
                        @endif
                    </div>
                </div>
            @endif

            <div class="order-summary">
                <h3>Informasi Pesanan</h3>
                <div class="order-info">
                    <div class="info-item">
                        <span class="info-label">Nomor Pesanan:</span>
                        <span class="info-value">#{{ $order->uuid }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Pesanan:</span>
                        <span class="info-value">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Metode Pengiriman:</span>
                        <span class="info-value">{{ ucfirst($order->shipping_method) }}</span>
                    </div>
                    @if ($order->latestPayment && $order->latestPayment->payment_type)
                        <div class="info-item">
                            <span class="info-label">Metode Pembayaran:</span>
                            <span
                                class="info-value">{{ ucfirst(str_replace('_', ' ', $order->latestPayment->payment_type)) }}</span>
                        </div>
                    @endif
                    {{-- Tanggal Dibayar (jika sudah bayar) --}}
                    @if ($order->latestPayment && $order->latestPayment->paid_at)
                        <div class="info-item">
                            <span class="info-label">Tanggal Dibayar:</span>
                            <span
                                class="info-value success">{{ \Carbon\Carbon::parse($order->latestPayment->paid_at)->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                    {{-- Batas Waktu Pembayaran (jika pending/expired) --}}
                    @if (
                        $order->latestPayment &&
                            $order->latestPayment->expired_at &&
                            in_array($order->latestPayment->status, ['pending', 'expired']))
                        <div class="info-item">
                            <span class="info-label">Batas Waktu Pembayaran:</span>
                            <span
                                class="info-value {{ $order->latestPayment->status == 'expired' ? 'expired' : 'pending' }}">
                                {{ \Carbon\Carbon::parse($order->latestPayment->expired_at)->format('d M Y H:i') }}
                                @if ($order->latestPayment->status == 'pending' && \Carbon\Carbon::parse($order->latestPayment->expired_at)->isFuture())
                                    <small style="display: block; font-size: 12px; color: #9ca3af;">
                                        ({{ \Carbon\Carbon::parse($order->latestPayment->expired_at)->diffForHumans() }})
                                    </small>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @if (in_array($type, ['processed', 'cancelled', 'failed']) && !empty($order->note))
                <div class="order-summary">
                    <h3>Catatan</h3>
                    <div class="order-info" style="white-space: pre-line">{{ $order->note }}</div>
                </div>
            @endif

            <h3 style="font-size: 18px; font-weight: 600; color: #374151;">Produk yang Dipesan</h3>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <div class="product-name">{{ $item->product->name ?? 'Produk tidak ditemukan' }}</div>
                                @if ($item->product && $item->product->category)
                                    <div style="font-size: 12px; color: #9ca3af;">{{ $item->product->category->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="price-breakdown">
                <h3>Rincian Biaya</h3>

                <div class="price-item">
                    <span class="price-label">Subtotal Produk:</span>
                    <span class="price-value">Rp {{ number_format($order->sub_total, 0, ',', '.') }}</span>
                </div>

                @if ($order->taxes && $order->taxes->count() > 0)
                    @foreach ($order->taxes as $tax)
                        <div class="price-item">
                            <span class="price-label">
                                {{ $tax->name }}
                                @if ($tax->percent)
                                    ({{ $tax->percent }}%)
                                @endif
                                :
                            </span>
                            <span class="price-value">Rp {{ number_format($tax->pivot->amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @endif

                @if ($order->shipping_method === 'delivery')
                    <div class="price-item">
                        <span class="price-label">Biaya Pengiriman:</span>
                        <span class="price-value">Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="price-item total">
                    <span class="price-label">Total Pembayaran:</span>
                    <span class="price-value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($type == 'success')
                <div class="footer-message">
                    <p><strong>Langkah Selanjutnya:</strong></p>
                    <p>Pesanan Anda akan segera diproses oleh tim kami. Anda akan mendapat notifikasi ketika pesanan
                        siap untuk {{ $order->shipping_method === 'pickup' ? 'diambil' : 'dikirim' }}.</p>
                </div>
            @elseif($type == 'pending')
                <div class="footer-message">
                    <p><strong>Jangan lupa untuk menyelesaikan pembayaran!</strong></p>
                    <p>Silakan login ke akun Anda untuk melanjutkan pembayaran atau gunakan link pembayaran yang telah
                        dikirimkan sebelumnya.</p>
                </div>
            @elseif(in_array($type, ['expired', 'cancelled', 'failed']))
                <div class="footer-message">
                    <p><strong>Ingin memesan lagi?</strong></p>
                    <p>Anda dapat membuat pesanan baru melalui website kami. Jika ada pertanyaan, jangan ragu untuk
                        menghubungi customer service.</p>
                </div>
            @elseif($type == 'processed')
                <div class="footer-message">
                    <p><strong>Informasi Penting:</strong></p>
                    @if ($order->shipping_method === 'pickup')
                        <p>Silakan datang ke gudang kami dengan menunjukkan nomor pesanan ini untuk mengambil pesanan
                            Anda.</p>
                        <p>Komp. Kopo Plaza, Komp, Jl. Peta No.7-8 Blok F, Suka Asih, Bojongloa Kaler, Bandung City, West Java 40242</p>
                    @else
                        <p>Pesanan Anda akan segera dikirimkan. Tim kami akan menghubungi Anda untuk koordinasi jadwal
                            dan lokasi pengiriman.</p>
                    @endif
                </div>
            @endif

        </div>

        <div class="footer">
            <p><strong>PT. Gurita Mandala Persada</strong></p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
            <p>Jika ada pertanyaan, silakan hubungi customer service kami di <a href="mailto:admingmp@guritamandala.com"
                    style="color: #0392de; text-decoration: none;">admingmp@guritamandala.com</a>.</p>
            <p style="margin-top: 8px;">© {{ date('Y') }} PT. Gurita Mandala Persada. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
