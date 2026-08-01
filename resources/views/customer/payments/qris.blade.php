@php
    $isPaid = $payment->status === \App\Models\Payment::STATUS_SUCCESS;

    $isFailed = $payment->status === \App\Models\Payment::STATUS_REJECTED;

    $isPending = !$isPaid && !$isFailed;

    /*
     * Perlindungan untuk data expiry lama yang
     * sebelumnya tersimpan dengan timezone salah.
     *
     * QRIS pada aplikasi ini berlaku 15 menit.
     */
    $effectiveExpiresAt = $payment->expires_at;

    if (
        $effectiveExpiresAt &&
        $payment->created_at &&
        $effectiveExpiresAt->greaterThan($payment->created_at->copy()->addMinutes(20))
    ) {
        $effectiveExpiresAt = $payment->created_at->copy()->addMinutes(15);
    }

    if (!$effectiveExpiresAt && $payment->created_at) {
        $effectiveExpiresAt = $payment->created_at->copy()->addMinutes(15);
    }

    $tableToken = $order->cafeTable?->qr_token;

    $orderStatusUrl = $tableToken
        ? route('customer.orders.success', [
            'token' => $tableToken,
            'order' => $order,
        ])
        : '#';

    $tableToken = $order->cafeTable?->qr_token;

    /*
     * Halaman utama pelanggan berdasarkan
     * QR Code meja yang digunakan.
     */
    $mainMenuUrl = $tableToken
        ? route('customer.menu', [
            'token' => $tableToken,
        ])
        : url('/');
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <meta content="{{ csrf_token() }}" name="csrf-token">

    <title>
        Pembayaran QRIS - Second Cafe
    </title>

    <style>
        :root {
            --primary: #f59e0b;
            --primary-dark: #b45309;
            --primary-soft: #fffbeb;
            --coffee: #292018;
            --coffee-soft: #f5f1ed;
            --success: #15803d;
            --success-soft: #f0fdf4;
            --danger: #dc2626;
            --danger-soft: #fef2f2;
            --warning: #c2410c;
            --warning-soft: #fff7ed;
            --info: #1d4ed8;
            --info-soft: #eff6ff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --surface: #ffffff;
            --background: #f8fafc;
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
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
            background:
                radial-gradient(circle at top,
                    #fff7ed 0,
                    transparent 35%),
                var(--background);
            color: var(--text);
            font-family:
                Inter,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        button,
        a {
            font: inherit;
        }

        [hidden] {
            display: none !important;
        }

        .page-shell {
            width: min(920px,
                    calc(100% - 32px));
            margin-inline: auto;
            padding: 24px 0 48px;
        }

        .top-navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--coffee);
            font-size: 16px;
            font-weight: 900;
            text-decoration: none;
        }

        .brand-icon {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 12px;
            background: var(--coffee);
            color: white;
            font-size: 19px;
        }

        .table-badge {
            padding: 8px 13px;
            border: 1px solid #fde68a;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 900;
        }

        .payment-card {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 26px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .payment-header {
            padding: 26px 28px 22px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .qris-logo {
            display: grid;
            width: 54px;
            height: 54px;
            place-items: center;
            margin: 0 auto 13px;
            border-radius: 17px;
            background: var(--primary-soft);
            font-size: 27px;
        }

        .payment-header h1 {
            margin: 0;
            font-size: clamp(24px,
                    4vw,
                    31px);
            line-height: 1.2;
        }

        .payment-header p {
            max-width: 560px;
            margin: 9px auto 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .order-summary {
            display: grid;
            grid-template-columns:
                repeat(3,
                    minmax(0, 1fr));
            gap: 12px;
            padding: 18px 22px;
            background: var(--primary-soft);
        }

        .summary-item {
            min-width: 0;
            padding: 12px 14px;
            border: 1px solid rgba(245, 158, 11, 0.18);
            border-radius: 14px;
            background:
                rgba(255, 255, 255, 0.72);
        }

        .summary-label {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
        }

        .summary-value {
            display: block;
            overflow: hidden;
            font-size: 13px;
            font-weight: 900;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .summary-value.total {
            color: var(--primary-dark);
            font-size: 19px;
        }

        .payment-content {
            display: grid;
            grid-template-columns:
                minmax(320px, 1fr) minmax(280px, 0.85fr);
            min-height: 430px;
        }

        .payment-visual {
            display: flex;
            min-width: 0;
            padding: 27px;
            align-items: center;
            justify-content: center;
            border-right: 1px solid var(--border);
        }

        .qr-panel {
            width: 100%;
            text-align: center;
        }

        .qr-frame {
            position: relative;
            width: min(300px,
                    100%);
            margin-inline: auto;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: white;
            box-shadow:
                0 12px 30px rgba(15, 23, 42, 0.07);
        }

        .qr-frame::before {
            position: absolute;
            inset: -1px;
            border: 2px solid rgba(245, 158, 11, 0.28);
            border-radius: 22px;
            content: "";
            pointer-events: none;
        }

        .qr-image {
            display: block;
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
        }

        .status-box {
            display: flex;
            width: min(380px,
                    100%);
            min-height: 46px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin: 17px auto 0;
            padding: 11px 14px;
            border-radius: 13px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.45;
        }

        .status-box.pending {
            background:
                var(--warning-soft);
            color: var(--warning);
        }

        .status-box.success {
            background:
                var(--success-soft);
            color: var(--success);
        }

        .status-box.failed {
            background:
                var(--danger-soft);
            color: var(--danger);
        }

        .status-indicator {
            width: 9px;
            height: 9px;
            flex: 0 0 9px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-box.pending .status-indicator {
            animation:
                pulse 1.2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.35;
                transform: scale(0.7);
            }
        }

        .countdown {
            margin: 11px 0 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .countdown strong {
            color: var(--text);
            font-size: 14px;
        }

        .countdown.danger,
        .countdown.danger strong {
            color: var(--danger);
        }

        .payment-information {
            min-width: 0;
            padding: 27px;
        }

        .information-title {
            margin: 0;
            font-size: 18px;
        }

        .information-description {
            margin: 7px 0 19px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .payment-apps {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 22px;
        }

        .app-badge {
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: #ffffff;
            color: #374151;
            font-size: 10px;
            font-weight: 800;
        }

        .instructions {
            margin: 0;
            padding: 0;
            list-style: none;
            counter-reset: payment-step;
        }

        .instructions li {
            position: relative;
            min-height: 34px;
            margin-bottom: 12px;
            padding-left: 43px;
            color: #4b5563;
            font-size: 12px;
            line-height: 1.55;
            counter-increment:
                payment-step;
        }

        .instructions li::before {
            position: absolute;
            top: 0;
            left: 0;
            display: grid;
            width: 30px;
            height: 30px;
            place-items: center;
            border-radius: 10px;
            background:
                var(--coffee-soft);
            color: var(--coffee);
            content:
                counter(payment-step);
            font-size: 11px;
            font-weight: 900;
        }

        .security-notice {
            margin-top: 20px;
            padding: 12px 13px;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            background: var(--info-soft);
            color: #1e40af;
            font-size: 10px;
            line-height: 1.6;
        }

        .result-panel {
            width: 100%;
            padding: 30px;
            text-align: center;
        }

        .result-icon {
            display: grid;
            width: 74px;
            height: 74px;
            place-items: center;
            margin: 0 auto 17px;
            border-radius: 24px;
            font-size: 38px;
        }

        .result-icon.success {
            background:
                var(--success-soft);
        }

        .result-icon.failed {
            background:
                var(--danger-soft);
        }

        .result-panel h2 {
            margin: 0;
            font-size: 24px;
        }

        .result-panel p {
            max-width: 390px;
            margin: 10px auto 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 19px 22px;
            border-top: 1px solid var(--border);
            background: #fafafa;
        }

        .button {
            display: inline-flex;
            min-height: 47px;
            flex: 1;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 17px;
            border: 0;
            border-radius: 13px;
            background: var(--coffee);
            color: white;
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
            text-align: center;
            text-decoration: none;
            transition:
                opacity 0.2s,
                transform 0.2s,
                box-shadow 0.2s;
        }

        .button:hover {
            transform:
                translateY(-1px);
            box-shadow:
                0 7px 18px rgba(41, 32, 24, 0.18);
        }

        .button.secondary {
            border: 1px solid var(--border);
            background: white;
            color: var(--text);
        }

        .button.danger {
            background: var(--danger);
        }

        .button:disabled {
            cursor: wait;
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 740px) {
            .page-shell {
                width: min(560px,
                        calc(100% - 22px));
                padding-top: 15px;
            }

            .payment-card {
                border-radius: 20px;
            }

            .order-summary {
                grid-template-columns:
                    minmax(0, 1fr);
                gap: 8px;
            }

            .payment-content {
                grid-template-columns:
                    minmax(0, 1fr);
            }

            .payment-visual {
                border-right: 0;
                border-bottom:
                    1px solid var(--border);
            }
        }

        @media (max-width: 480px) {
            .page-shell {
                width: calc(100% - 16px);
                padding-bottom: 20px;
            }

            .top-navigation {
                padding-inline: 5px;
            }

            .payment-header {
                padding: 22px 17px 18px;
            }

            .order-summary,
            .payment-visual,
            .payment-information {
                padding: 17px;
            }

            .qr-frame {
                width: min(270px,
                        100%);
            }

            .actions {
                padding: 15px;
                flex-direction: column;
            }

            .button {
                width: 100%;
                flex: none;
            }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <nav class="top-navigation">
            <a class="brand" href="{{ $mainMenuUrl }}">
                <span class="brand-icon">
                    ☕
                </span>

                <span>Second Cafe</span>
            </a>

            <span class="table-badge">
                Meja
                {{ $order->cafeTable?->table_number ?? '-' }}
            </span>
        </nav>

        <main class="payment-card">
            <header class="payment-header">
                <div class="qris-logo">
                    ▦
                </div>

                <h1>Pembayaran QRIS</h1>

                <p>
                    Scan menggunakan aplikasi pembayaran
                    yang mendukung QRIS. Status pembayaran
                    akan diperbarui secara otomatis.
                </p>
            </header>

            <section class="order-summary">
                <div class="summary-item">
                    <span class="summary-label">
                        Kode Pesanan
                    </span>

                    <strong class="summary-value">
                        {{ $order->order_code }}
                    </strong>
                </div>

                <div class="summary-item">
                    <span class="summary-label">
                        Nama Pelanggan
                    </span>

                    <strong class="summary-value">
                        {{ $order->customer_name }}
                    </strong>
                </div>

                <div class="summary-item">
                    <span class="summary-label">
                        Total Pembayaran
                    </span>

                    <strong class="summary-value total">
                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                    </strong>
                </div>
            </section>

            <div class="payment-content">
                <section @if (!$isPending) hidden @endif class="payment-visual" id="pending-section">
                    <div class="qr-panel">
                        @if (filled($payment->qr_code_url))
                            <div class="qr-frame">
                                <img alt="QRIS pembayaran {{ $order->order_code }}" class="qr-image"
                                    src="{{ $payment->qr_code_url }}">
                            </div>
                        @else
                            <div class="result-icon failed">
                                ⚠️
                            </div>

                            <p>
                                QR pembayaran belum tersedia.
                            </p>
                        @endif

                        <div class="status-box pending" id="payment-status">
                            <span class="status-indicator"></span>

                            <span id="payment-status-text">
                                Menunggu pembayaran QRIS
                            </span>
                        </div>

                        <p class="countdown" id="countdown">
                            Sisa waktu
                            <strong id="countdown-value">
                                --:--
                            </strong>
                        </p>
                    </div>
                </section>

                <section @if (!$isPaid) hidden @endif class="payment-visual" id="success-section">
                    <div class="result-panel">
                        <div class="result-icon success">
                            ✅
                        </div>

                        <h2>Pembayaran Berhasil</h2>

                        <p id="success-message">
                            Pembayaran telah diterima.
                            Pesanan Anda akan segera
                            diproses oleh tim Second Cafe.
                        </p>
                    </div>
                </section>

                <section @if (!$isFailed) hidden @endif class="payment-visual" id="failed-section">
                    <div class="result-panel">
                        <div class="result-icon failed">
                            ❌
                        </div>

                        <h2>Pembayaran Belum Berhasil</h2>

                        <p id="failed-message">
                            {{ $payment->rejection_reason ?: 'QR pembayaran sudah tidak berlaku.' }}
                        </p>
                    </div>
                </section>

                <aside @if (!$isPending) hidden @endif class="payment-information"
                    id="instructions-section">
                    <h2 class="information-title">
                        Cara Membayar
                    </h2>

                    <p class="information-description">
                        Gunakan salah satu aplikasi
                        pembayaran berikut.
                    </p>

                    <div class="payment-apps">
                        <span class="app-badge">DANA</span>
                        <span class="app-badge">BRImo</span>
                        <span class="app-badge">Livin'</span>
                        <span class="app-badge">GoPay</span>
                        <span class="app-badge">OVO</span>
                        <span class="app-badge">ShopeePay</span>
                    </div>

                    <ol class="instructions">
                        <li>
                            Buka aplikasi pembayaran yang
                            mendukung QRIS.
                        </li>

                        <li>
                            Pilih menu Scan QR atau Bayar.
                        </li>

                        <li>
                            Scan QR pembayaran yang tampil.
                        </li>

                        <li>
                            Periksa nama merchant dan
                            total pembayaran.
                        </li>

                        <li>
                            Konfirmasikan pembayaran
                            menggunakan PIN.
                        </li>
                    </ol>

                    <div class="security-notice">
                        Jangan menutup halaman ini sebelum
                        status pembayaran berhasil. Sistem
                        akan memeriksa pembayaran setiap
                        lima detik.
                    </div>
                </aside>

                <aside @if ($isPending) hidden @endif class="payment-information"
                    id="result-information">
                    <h2 class="information-title">
                        Informasi Pesanan
                    </h2>

                    <p class="information-description">
                        Status pesanan dapat dipantau
                        melalui halaman pesanan pelanggan.
                    </p>

                    <div class="security-notice">
                        Kode pesanan:
                        <strong>
                            {{ $order->order_code }}
                        </strong>
                    </div>
                </aside>
            </div>

            <footer @if (!$isPending) hidden @endif class="actions" id="pending-actions">
                @if (filled($payment->qr_code_url))
                    <a class="button secondary" href="{{ $payment->qr_code_url }}" rel="noopener noreferrer"
                        target="_blank">
                        Buka QR
                    </a>
                @endif

                <button class="button" id="check-status-button" type="button">
                    <span>Periksa Status</span>
                </button>
            </footer>

            <footer @if (!$isPaid) hidden @endif class="actions" id="success-actions">
                <a class="button" href="{{ $mainMenuUrl }}">
                    Kembali ke Menu Utama
                </a>
            </footer>

            <footer @if (!$isFailed) hidden @endif class="actions" id="failed-actions">
                <a class="button secondary" href="{{ $mainMenuUrl }}">
                    Kembali ke Menu Utama
                </a>

                <button class="button danger" id="retry-payment-button" type="button">
                    Buat QR Baru
                </button>
            </footer>
        </main>
    </div>

    <script>
        const statusUrl = @json(route('customer.payment.qris.status', ['order' => $order]));

        const expiryTimestampSeconds = @json($effectiveExpiresAt?->timestamp);

        const initialState = {
            isPaid: @json($isPaid),
            isFailed: @json($isFailed),
            isPending: @json($isPending),
        };

        const pendingSection =
            document.getElementById(
                'pending-section'
            );

        const successSection =
            document.getElementById(
                'success-section'
            );

        const failedSection =
            document.getElementById(
                'failed-section'
            );

        const instructionsSection =
            document.getElementById(
                'instructions-section'
            );

        const resultInformation =
            document.getElementById(
                'result-information'
            );

        const pendingActions =
            document.getElementById(
                'pending-actions'
            );

        const successActions =
            document.getElementById(
                'success-actions'
            );

        const failedActions =
            document.getElementById(
                'failed-actions'
            );

        const statusBox =
            document.getElementById(
                'payment-status'
            );

        const statusText =
            document.getElementById(
                'payment-status-text'
            );

        const countdown =
            document.getElementById(
                'countdown'
            );

        const countdownValue =
            document.getElementById(
                'countdown-value'
            );

        const failedMessage =
            document.getElementById(
                'failed-message'
            );

        const successMessage =
            document.getElementById(
                'success-message'
            );

        const checkStatusButton =
            document.getElementById(
                'check-status-button'
            );

        const retryPaymentButton =
            document.getElementById(
                'retry-payment-button'
            );

        let checkingStatus = false;
        let pollingInterval = null;
        let countdownInterval = null;
        let expiryHandled = false;

        function stopAutomaticChecks() {
            if (pollingInterval) {
                clearInterval(
                    pollingInterval
                );

                pollingInterval = null;
            }

            if (countdownInterval) {
                clearInterval(
                    countdownInterval
                );

                countdownInterval = null;
            }
        }

        function showPending(
            message = 'Menunggu pembayaran QRIS'
        ) {
            pendingSection.hidden = false;
            successSection.hidden = true;
            failedSection.hidden = true;

            instructionsSection.hidden = false;
            resultInformation.hidden = true;

            pendingActions.hidden = false;
            successActions.hidden = true;
            failedActions.hidden = true;

            statusText.textContent = message;
        }

        function showSuccess(
            message = 'Pembayaran berhasil diterima.'
        ) {
            stopAutomaticChecks();

            pendingSection.hidden = true;
            failedSection.hidden = true;
            successSection.hidden = false;

            instructionsSection.hidden = true;
            resultInformation.hidden = false;

            pendingActions.hidden = true;
            failedActions.hidden = true;
            successActions.hidden = false;

            successMessage.textContent = message;
        }

        function showFailed(
            message = 'Pembayaran belum berhasil.'
        ) {
            stopAutomaticChecks();

            pendingSection.hidden = true;
            successSection.hidden = true;
            failedSection.hidden = false;

            instructionsSection.hidden = true;
            resultInformation.hidden = false;

            pendingActions.hidden = true;
            successActions.hidden = true;
            failedActions.hidden = false;

            failedMessage.textContent = message;
        }

        async function checkPaymentStatus() {
            if (
                checkingStatus ||
                initialState.isPaid
            ) {
                return;
            }

            checkingStatus = true;

            if (checkStatusButton) {
                checkStatusButton.disabled = true;
                checkStatusButton.textContent =
                    'Memeriksa...';
            }

            try {
                const response = await fetch(
                    statusUrl, {
                        method: 'GET',

                        headers: {
                            Accept: 'application/json',

                            'X-Requested-With': 'XMLHttpRequest',
                        },

                        cache: 'no-store',
                    },
                );

                if (!response.ok) {
                    throw new Error(
                        'Status pembayaran gagal diperiksa.'
                    );
                }

                const result =
                    await response.json();

                if (result.is_paid) {
                    showSuccess(
                        result.message ||
                        'Pembayaran berhasil diterima.'
                    );

                    return;
                }

                if (result.is_failed) {
                    showFailed(
                        result.message ||
                        'Pembayaran belum berhasil.'
                    );

                    return;
                }

                showPending(
                    result.message ||
                    'Menunggu pembayaran QRIS'
                );
            } catch (error) {
                if (statusText) {
                    statusText.textContent =
                        'Koneksi terputus. Sistem akan mencoba kembali.';
                }
            } finally {
                checkingStatus = false;

                if (checkStatusButton) {
                    checkStatusButton.disabled = false;
                    checkStatusButton.textContent =
                        'Periksa Status';
                }
            }
        }

        function updateCountdown() {
            if (
                !expiryTimestampSeconds ||
                !countdownValue
            ) {
                if (countdown) {
                    countdown.textContent =
                        'QR berlaku selama 15 menit.';
                }

                return;
            }

            /*
             * Timestamp PHP menggunakan detik,
             * sedangkan JavaScript milidetik.
             */
            const expiryMilliseconds =
                Number(
                    expiryTimestampSeconds
                ) * 1000;

            const remainingMilliseconds =
                expiryMilliseconds -
                Date.now();

            if (
                remainingMilliseconds <= 0
            ) {
                countdown.innerHTML =
                    'Waktu pembayaran ' +
                    '<strong>telah habis</strong>';

                countdown.classList.add(
                    'danger'
                );

                if (!expiryHandled) {
                    expiryHandled = true;

                    checkPaymentStatus();
                }

                return;
            }

            const totalSeconds =
                Math.floor(
                    remainingMilliseconds /
                    1000
                );

            const minutes =
                Math.floor(
                    totalSeconds / 60
                );

            const seconds =
                totalSeconds % 60;

            countdownValue.textContent =
                String(minutes) +
                ':' +
                String(seconds)
                .padStart(2, '0');

            if (totalSeconds <= 60) {
                countdown.classList.add(
                    'danger'
                );
            }
        }

        if (checkStatusButton) {
            checkStatusButton.addEventListener(
                'click',
                checkPaymentStatus
            );
        }

        if (retryPaymentButton) {
            retryPaymentButton.addEventListener(
                'click',
                () => {
                    retryPaymentButton.disabled =
                        true;

                    retryPaymentButton.textContent =
                        'Membuat QR...';

                    window.location.reload();
                }
            );
        }

        if (initialState.isPending) {
            updateCountdown();
            checkPaymentStatus();

            countdownInterval = setInterval(
                updateCountdown,
                1000
            );

            pollingInterval = setInterval(
                checkPaymentStatus,
                5000
            );
        }
    </script>
</body>

</html>
