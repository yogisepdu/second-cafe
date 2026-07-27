<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>
        Pesanan {{ $order->cashier_code }} - Second Cafe
    </title>

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
            --danger: #dc2626;
            --danger-soft: #fef2f2;
            --info: #1d4ed8;
            --info-soft: #eff6ff;
            --shadow:
                0 16px 40px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
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

        /*
        |--------------------------------------------------------------------------
        | Hero
        |--------------------------------------------------------------------------
        */

        .hero {
            padding: 34px 24px 27px;
            background:
                linear-gradient(145deg,
                    #fffbeb,
                    #ffffff);
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
            font-size: 31px;
            transition:
                background 0.25s,
                border-color 0.25s,
                color 0.25s;
        }

        .status-icon.is-waiting {
            border-color: #fde68a;
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .status-icon.is-success {
            border-color: #bbf7d0;
            background: var(--success-soft);
            color: var(--success);
        }

        .status-icon.is-danger {
            border-color: #fecaca;
            background: var(--danger-soft);
            color: var(--danger);
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
            font-size:
                clamp(25px, 5vw, 34px);
            line-height: 1.2;
        }

        .hero-description {
            max-width: 500px;
            margin: 10px auto 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .order-code-label {
            margin: 19px 0 6px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .order-code {
            display: inline-block;
            padding: 11px 18px;
            border: 2px dashed var(--primary);
            border-radius: 12px;
            background: #ffffff;
            color: var(--coffee);
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 3px;
        }

        .order-code-help {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        .content {
            padding: 24px;
        }

        /*
        |--------------------------------------------------------------------------
        | Instruction
        |--------------------------------------------------------------------------
        */

        .instruction {
            margin-bottom: 16px;
            padding: 15px 16px;
            border-radius: 14px;
            font-size: 12px;
            line-height: 1.65;
        }

        .instruction.cashier {
            border: 1px solid #fde68a;
            background: var(--primary-soft);
            color: #92400e;
        }

        .instruction.online {
            border: 1px solid #bfdbfe;
            background: var(--info-soft);
            color: #1e40af;
        }

        .instruction.paid {
            border: 1px solid #bbf7d0;
            background: var(--success-soft);
            color: #166534;
        }

        .instruction.cancelled {
            border: 1px solid #fecaca;
            background: var(--danger-soft);
            color: #991b1b;
        }

        .instruction strong {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
        }

        /*
        |--------------------------------------------------------------------------
        | Live status
        |--------------------------------------------------------------------------
        */

        .status-monitor {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 22px;
            padding: 11px 13px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fafafa;
        }

        .status-monitor-text {
            min-width: 0;
        }

        .status-monitor-title {
            display: block;
            font-size: 11px;
            font-weight: 900;
        }

        .last-updated {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 9px;
            line-height: 1.4;
        }

        .live-indicator {
            display: inline-flex;
            flex-shrink: 0;
            align-items: center;
            gap: 6px;
            padding: 6px 9px;
            border-radius: 999px;
            background: var(--success-soft);
            color: var(--success);
            font-size: 9px;
            font-weight: 900;
        }

        .live-indicator.offline {
            background: #f3f4f6;
            color: var(--muted);
        }

        .live-indicator.finished {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            animation:
                live-pulse 1.5s infinite;
        }

        .live-indicator.offline .live-dot,
        .live-indicator.finished .live-dot {
            animation: none;
        }

        @keyframes live-pulse {
            50% {
                opacity: 0.25;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Information
        |--------------------------------------------------------------------------
        */

        .info-grid {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
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

        /*
        |--------------------------------------------------------------------------
        | Order items
        |--------------------------------------------------------------------------
        */

        .order-items {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 15px;
        }

        .order-item {
            display: grid;
            grid-template-columns:
                minmax(0, 1fr) auto;
            gap: 15px;
            padding: 14px 15px;
            border-bottom:
                1px solid var(--border);
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

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

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

        .total-line strong {
            color: var(--text);
        }

        .grand-total {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 8px;
            padding-top: 14px;
            border-top:
                1px solid var(--border);
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

        /*
        |--------------------------------------------------------------------------
        | Actions
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 540px) {
            body {
                padding: 14px 10px;
            }

            .receipt-card {
                border-radius: 19px;
            }

            .hero {
                padding:
                    27px 17px 23px;
            }

            .content {
                padding: 18px 17px;
            }

            .order-code {
                font-size: 20px;
            }

            .status-monitor {
                align-items: flex-start;
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns:
                    minmax(0, 1fr);
            }

            .order-item {
                grid-template-columns:
                    minmax(0, 1fr);
                gap: 8px;
            }

            .item-price {
                justify-self: start;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Print
        |--------------------------------------------------------------------------
        */

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .receipt-card {
                border: 0;
                box-shadow: none;
            }

            .status-monitor,
            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    @php
        $isPaid = $order->payment_status === \App\Models\Order::PAYMENT_STATUS_PAID;

        $isCancelled = $order->status === \App\Models\Order::STATUS_CANCELLED;

        $isCashier = $order->payment_method === \App\Models\Order::PAYMENT_METHOD_CASHIER;

        if ($isCancelled) {
            $statusIcon = '×';
            $statusIconClass = 'is-danger';

            $statusHeading = 'Pesanan Dibatalkan';

            $statusDescription = 'Pesanan ini telah dibatalkan. Silakan hubungi petugas untuk informasi lebih lanjut.';

            $instructionClass = 'cancelled';

            $instructionTitle = 'Pesanan tidak dapat diproses.';

            $instructionMessage = 'Silakan hubungi petugas Second Cafe.';
        } elseif (!$isPaid) {
            $statusIcon = '⌛';
            $statusIconClass = 'is-waiting';

            if ($isCashier) {
                $statusHeading = 'Menunggu Pembayaran';

                $statusDescription = 'Tunjukkan kode bayar berikut kepada petugas kasir.';

                $instructionClass = 'cashier';

                $instructionTitle = 'Silakan lakukan pembayaran di kasir.';

                $instructionMessage = "Tunjukkan kode bayar {$order->cashier_code} kepada petugas kasir.";
            } else {
                $statusHeading = 'Menunggu Pembayaran Online';

                $statusDescription = 'Selesaikan pembayaran online agar pesanan dapat segera diproses.';

                $instructionClass = 'online';

                $instructionTitle = 'Pembayaran belum selesai.';

                $instructionMessage = 'Silakan selesaikan pembayaran QRIS atau transfer bank.';
            }
        } else {
            $statusIcon = '✓';
            $statusIconClass = 'is-success';
            $instructionClass = 'paid';

            switch ($order->status) {
                case \App\Models\Order::STATUS_PROCESSING:
                    $statusHeading = 'Pesanan Sedang Diproses';

                    $statusDescription = 'Tim Second Cafe sedang menyiapkan pesanan Anda.';

                    $instructionTitle = 'Pesanan sedang disiapkan.';

                    $instructionMessage = 'Mohon menunggu di meja. Kami akan memberi tahu ketika pesanan siap.';
                    break;

                case \App\Models\Order::STATUS_READY:
                    $statusHeading = 'Pesanan Sudah Siap';

                    $statusDescription = 'Pesanan Anda telah selesai disiapkan.';

                    $instructionTitle = 'Pesanan siap disajikan.';

                    $instructionMessage = 'Petugas akan segera mengantarkan pesanan ke meja Anda.';
                    break;

                case \App\Models\Order::STATUS_COMPLETED:
                    $statusHeading = 'Pesanan Selesai';

                    $statusDescription = 'Terima kasih telah melakukan pemesanan di Second Cafe.';

                    $instructionTitle = 'Pesanan telah selesai.';

                    $instructionMessage = 'Selamat menikmati dan terima kasih atas kunjungannya.';
                    break;

                default:
                    $statusHeading = 'Pembayaran Berhasil';

                    $statusDescription = 'Pembayaran telah diterima dan pesanan sudah masuk ke sistem.';

                    $instructionTitle = 'Pembayaran berhasil diterima.';

                    $instructionMessage = 'Pesanan Anda telah diterima dan akan segera diproses.';
                    break;
            }
        }

        $statusPollingUrl = route('customer.orders.status', [
            'token' => $cafeTable->qr_token,
            'order' => $order,
        ]);
    @endphp

    <main class="container">
        <article class="receipt-card">
            <header class="hero">
                <div aria-hidden="true" class="status-icon {{ $statusIconClass }}" id="status-icon">
                    {{ $statusIcon }}
                </div>

                <p class="brand">
                    Second Cafe
                </p>

                <h1 id="status-heading">
                    {{ $statusHeading }}
                </h1>

                <p aria-live="polite" class="hero-description" id="status-description">
                    {{ $statusDescription }}
                </p>

                <p class="order-code-label">
                    Kode Bayar
                </p>

                <div class="order-code">
                    {{ $order->cashier_code }}
                </div>

                <p class="order-code-help">
                    Tunjukkan kode ini kepada petugas kasir.
                </p>
            </header>

            <div class="content">
                <div aria-live="polite" class="instruction {{ $instructionClass }}" id="payment-instruction">
                    <strong id="instruction-title">
                        {{ $instructionTitle }}
                    </strong>

                    <span id="instruction-message">
                        {{ $instructionMessage }}
                    </span>
                </div>

                <div class="status-monitor">
                    <div class="status-monitor-text">
                        <span class="status-monitor-title">
                            Status diperbarui otomatis
                        </span>

                        <span class="last-updated" id="last-updated">
                            Memeriksa status terbaru...
                        </span>
                    </div>

                    <span class="live-indicator" id="live-indicator">
                        <span class="live-dot"></span>

                        <span id="live-label">
                            Terhubung
                        </span>
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-box">
                        <span class="info-label">
                            Nomor Meja
                        </span>

                        <span class="info-value">
                            {{ $cafeTable->table_number }}
                        </span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">
                            Tanggal Pesanan
                        </span>

                        <span class="info-value">
                            {{ $order->ordered_at?->format('d M Y, H:i') ?? '-' }}
                            WIB
                        </span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">
                            Nama Pelanggan
                        </span>

                        <span class="info-value">
                            {{ $order->customer_name }}
                        </span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">
                            Metode Pembayaran
                        </span>

                        <span class="info-value">
                            {{ $order->payment_method_label }}
                        </span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">
                            Status Pesanan
                        </span>

                        <span class="info-value" id="order-status">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <div class="info-box">
                        <span class="info-label">
                            Status Pembayaran
                        </span>

                        <span class="info-value" id="payment-status">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                </div>

                <h2 class="section-title">
                    Detail Pesanan
                </h2>

                <div class="order-items">
                    @foreach ($order->items as $item)
                        <div class="order-item">
                            <div>
                                <p class="item-name">
                                    {{ $item->menu_name }}
                                </p>

                                <p class="item-meta">
                                    {{ $item->quantity }}
                                    ×
                                    Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                </p>

                                @if (!empty($item->selected_options))
                                    <p class="item-options">
                                        @foreach ($item->selected_options as $option)
                                            {{ $option['group'] ?? 'Pilihan' }}:
                                            {{ $option['option'] ?? '-' }}
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </p>
                                @endif

                                @if (filled($item->notes))
                                    <p class="item-notes">
                                        Catatan:
                                        {{ $item->notes }}
                                    </p>
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

                        <strong>
                            Rp{{ number_format((float) $order->subtotal, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="total-line">
                        <span>Biaya layanan</span>

                        <strong>Rp0</strong>
                    </div>

                    <div class="grand-total">
                        <span>Total</span>

                        <span>
                            Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if (filled($order->notes))
                    <div class="general-notes">
                        <strong>
                            Catatan umum:
                        </strong>

                        <br>

                        {{ $order->notes }}
                    </div>
                @endif

                <div class="actions">
                    <a class="button button-primary"
                        href="{{ route('customer.menu', [
                            'token' => $cafeTable->qr_token,
                        ]) }}">
                        Kembali ke Menu
                    </a>

                    <button class="button button-secondary" onclick="window.print()" type="button">
                        Cetak Struk
                    </button>
                </div>
            </div>
        </article>
    </main>

    <script>
        const statusUrl =
            {{ \Illuminate\Support\Js::from($statusPollingUrl) }};

        const statusIcon =
            document.getElementById(
                'status-icon'
            );

        const statusHeading =
            document.getElementById(
                'status-heading'
            );

        const statusDescription =
            document.getElementById(
                'status-description'
            );

        const paymentInstruction =
            document.getElementById(
                'payment-instruction'
            );

        const instructionTitle =
            document.getElementById(
                'instruction-title'
            );

        const instructionMessage =
            document.getElementById(
                'instruction-message'
            );

        const orderStatus =
            document.getElementById(
                'order-status'
            );

        const paymentStatus =
            document.getElementById(
                'payment-status'
            );

        const lastUpdated =
            document.getElementById(
                'last-updated'
            );

        const liveIndicator =
            document.getElementById(
                'live-indicator'
            );

        const liveLabel =
            document.getElementById(
                'live-label'
            );

        let pollingTimer = null;
        let requestIsRunning = false;
        let pollingStopped = false;

        function renderStatus(data) {
            statusHeading.textContent =
                data.headline;

            statusDescription.textContent =
                data.message;

            instructionTitle.textContent =
                data.instruction_title;

            instructionMessage.textContent =
                data.instruction_message;

            orderStatus.textContent =
                data.order_status_label;

            paymentStatus.textContent =
                data.payment_status_label;

            lastUpdated.textContent =
                `Terakhir diperbarui: ${
                    data.updated_at_label
                }`;

            paymentInstruction.className =
                'instruction';

            statusIcon.className =
                'status-icon';

            if (data.is_cancelled) {
                statusIcon.textContent = '×';

                statusIcon.classList.add(
                    'is-danger'
                );

                paymentInstruction.classList.add(
                    'cancelled'
                );
            } else if (data.is_paid) {
                statusIcon.textContent = '✓';

                statusIcon.classList.add(
                    'is-success'
                );

                paymentInstruction.classList.add(
                    'paid'
                );
            } else {
                statusIcon.textContent = '⌛';

                statusIcon.classList.add(
                    'is-waiting'
                );

                paymentInstruction.classList.add(
                    data.payment_method ===
                    'cashier' ?
                    'cashier' :
                    'online'
                );
            }

            liveIndicator.className =
                'live-indicator';

            liveLabel.textContent =
                'Terhubung';

            /*
             * Berhenti mengecek status setelah
             * selesai atau dibatalkan.
             */
            if (
                data.order_status ===
                'selesai' ||
                data.is_cancelled
            ) {
                pollingStopped = true;

                if (pollingTimer !== null) {
                    clearInterval(
                        pollingTimer
                    );
                }

                liveIndicator.classList.add(
                    'finished'
                );

                liveLabel.textContent =
                    'Selesai';
            }
        }

        async function refreshOrderStatus() {
            if (
                requestIsRunning ||
                document.hidden ||
                pollingStopped
            ) {
                return;
            }

            requestIsRunning = true;

            try {
                const response = await fetch(
                    statusUrl, {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',
                        },

                        cache: 'no-store',
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'Status pesanan gagal dimuat.'
                    );
                }

                const result =
                    await response.json();

                renderStatus(result);
            } catch (error) {
                liveIndicator.className =
                    'live-indicator offline';

                liveLabel.textContent =
                    'Menghubungkan ulang';
            } finally {
                requestIsRunning = false;
            }
        }

        document.addEventListener(
            'visibilitychange',
            () => {
                if (
                    !document.hidden &&
                    !pollingStopped
                ) {
                    refreshOrderStatus();
                }
            }
        );

        /*
         * Ambil status langsung saat halaman dibuka.
         */
        refreshOrderStatus();

        /*
         * Periksa status setiap 5 detik.
         */
        pollingTimer = setInterval(
            refreshOrderStatus,
            5000
        );
    </script>
</body>

</html>
