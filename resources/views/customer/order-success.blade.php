<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Pesanan {{ $order->order_code }} - Second Cafe</title>

    <style>
        :root {
            --primary: #f59e0b;
            --primary-dark: #b45309;
            --primary-soft: #fffbeb;
            --coffee: #292018;
            --background: #f8fafc;
            --surface: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --success: #15803d;
            --success-soft: #f0fdf4;
            --info: #1d4ed8;
            --info-soft: #eff6ff;
            --shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            padding: 34px 16px;
            background: var(--background);
            color: var(--text);
            font-family: Inter, Arial, sans-serif;
        }

        button {
            font: inherit;
        }

        .container {
            width: min(720px, 100%);
            margin-inline: auto;
        }

        .receipt-card {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 26px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .hero {
            padding: 34px 24px 27px;
            background: linear-gradient(145deg, #fffbeb, #ffffff);
            text-align: center;
        }

        .status-icon {
            display: grid;
            width: 68px;
            height: 68px;
            margin: 0 auto 17px;
            place-items: center;
            border: 1px solid #fde68a;
            border-radius: 50%;
            background: #ffffff;
            color: var(--primary-dark);
            font-size: 31px;
        }

        .brand {
            margin: 0 0 7px;
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(25px, 5vw, 34px);
        }

        .hero-description {
            max-width: 500px;
            margin: 10px auto 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .order-code {
            display: inline-block;
            margin-top: 18px;
            padding: 10px 15px;
            border: 1px dashed #f59e0b;
            border-radius: 12px;
            background: #ffffff;
            color: var(--coffee);
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 0.8px;
        }

        .content {
            padding: 24px;
        }

        .instruction {
            margin-bottom: 22px;
            padding: 15px 16px;
            border-radius: 14px;
            font-size: 12px;
            line-height: 1.65;
        }

        .instruction.cashier {
            border: 1px solid #bbf7d0;
            background: var(--success-soft);
            color: #166534;
        }

        .instruction.online {
            border: 1px solid #bfdbfe;
            background: var(--info-soft);
            color: #1e40af;
        }

        .instruction strong {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .info-box {
            min-width: 0;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: #fafafa;
        }

        .info-label {
            display: block;
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .info-value {
            display: block;
            overflow-wrap: anywhere;
            font-size: 12px;
            font-weight: 800;
        }

        .section-title {
            margin: 0 0 12px;
            font-size: 17px;
        }

        .order-items {
            border: 1px solid var(--border);
            border-radius: 15px;
            overflow: hidden;
        }

        .order-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 15px;
            padding: 14px 15px;
            border-bottom: 1px solid var(--border);
        }

        .order-item:last-child {
            border-bottom: 0;
        }

        .item-name {
            margin: 0;
            font-size: 13px;
            font-weight: 900;
        }

        .item-meta,
        .item-notes,
        .item-options {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.5;
        }

        .item-price {
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .totals {
            margin-top: 18px;
            padding: 17px;
            border-radius: 15px;
            background: #fafafa;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 6px 0;
            color: var(--muted);
            font-size: 12px;
        }

        .grand-total {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 8px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            font-size: 18px;
            font-weight: 900;
        }

        .grand-total span:last-child {
            color: var(--primary-dark);
        }

        .general-notes {
            margin-top: 18px;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 13px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.55;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 22px;
        }

        .button {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            padding: 11px 16px;
            border: 0;
            border-radius: 13px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .button-primary {
            flex: 1;
            background: var(--coffee);
            color: #ffffff;
        }

        .button-secondary {
            border: 1px solid var(--border);
            background: #ffffff;
            color: var(--text);
        }

        @media (max-width: 540px) {
            body {
                padding: 14px 10px;
            }

            .receipt-card {
                border-radius: 19px;
            }

            .hero,
            .content {
                padding-right: 17px;
                padding-left: 17px;
            }

            .info-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .receipt-card {
                border: 0;
                box-shadow: none;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <article class="receipt-card">
            <header class="hero">
                <div class="status-icon">✓</div>
                <p class="brand">Second Cafe</p>
                <h1>Pesanan Berhasil Dibuat</h1>
                <p class="hero-description">
                    Simpan kode pesanan berikut untuk melihat atau mengonfirmasi transaksi kepada petugas.
                </p>
                <div class="order-code">{{ $order->order_code }}</div>
            </header>

            <div class="content">
                @if ($order->payment_method === \App\Models\Order::PAYMENT_METHOD_CASHIER)
                    <div class="instruction cashier">
                        <strong>Silakan lakukan pembayaran di kasir.</strong>
                        Tunjukkan kode pesanan <b>{{ $order->order_code }}</b> kepada petugas kasir. Pesanan akan
                        diproses setelah pembayaran dikonfirmasi.
                    </div>
                @else
                    <div class="instruction online">
                        <strong>Pesanan menunggu pembayaran online.</strong>
                        Integrasi QRIS dan transfer bank akan disambungkan pada tahap berikutnya. Pesanan saat ini telah
                        tercatat dengan aman.
                    </div>
                @endif

                <div class="info-grid">
                    <div class="info-box">
                        <span class="info-label">Nomor Meja</span>
                        <span class="info-value">{{ $cafeTable->table_number }}</span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Tanggal Pesanan</span>
                        <span class="info-value">
                            {{ $order->ordered_at?->format('d M Y, H:i') ?? '-' }} WIB
                        </span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Nama Pelanggan</span>
                        <span class="info-value">{{ $order->customer_name }}</span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Metode Pembayaran</span>
                        <span class="info-value">{{ $order->payment_method_label }}</span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Status Pesanan</span>
                        <span class="info-value">{{ $order->status_label }}</span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">Status Pembayaran</span>
                        <span class="info-value">{{ $order->payment_status_label }}</span>
                    </div>
                </div>

                <h2 class="section-title">Detail Pesanan</h2>

                <div class="order-items">
                    @foreach ($order->items as $item)
                        <div class="order-item">
                            <div>
                                <p class="item-name">{{ $item->menu_name }}</p>
                                <p class="item-meta">
                                    {{ $item->quantity }} ×
                                    Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                </p>

                                @if (!empty($item->selected_options))
                                    <p class="item-options">
                                        @foreach ($item->selected_options as $option)
                                            {{ $option['group'] ?? 'Pilihan' }}: {{ $option['option'] ?? '-' }}
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </p>
                                @endif

                                @if (filled($item->notes))
                                    <p class="item-notes">Catatan: {{ $item->notes }}</p>
                                @endif
                            </div>

                            <span class="item-price">
                                Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="totals">
                    <div class="total-line">
                        <span>Subtotal</span>
                        <strong>Rp{{ number_format((float) $order->subtotal, 0, ',', '.') }}</strong>
                    </div>

                    <div class="total-line">
                        <span>Biaya layanan</span>
                        <strong>Rp0</strong>
                    </div>

                    <div class="grand-total">
                        <span>Total</span>
                        <span>Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if (filled($order->notes))
                    <div class="general-notes">
                        <strong>Catatan umum:</strong><br>
                        {{ $order->notes }}
                    </div>
                @endif

                <div class="actions">
                    <a class="button button-primary"
                        href="{{ route('customer.menu', ['token' => $cafeTable->qr_token]) }}">
                        Kembali ke Menu
                    </a>

                    <button class="button button-secondary" onclick="window.print()" type="button">
                        Cetak Struk
                    </button>
                </div>
            </div>
        </article>
    </main>
</body>

</html>
