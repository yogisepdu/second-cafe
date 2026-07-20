<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Checkout - Second Cafe</title>

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
            --danger: #dc2626;
            --danger-soft: #fef2f2;
            --success: #15803d;
            --shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
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
            background: var(--background);
            color: var(--text);
            font-family: Inter, Arial, sans-serif;
        }

        button,
        input,
        textarea {
            font: inherit;
        }

        .container {
            width: min(1100px, calc(100% - 32px));
            margin-inline: auto;
        }

        .topbar {
            position: sticky;
            z-index: 50;
            top: 0;
            border-bottom: 1px solid rgba(229, 231, 235, 0.9);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(14px);
        }

        .topbar-content {
            display: flex;
            min-height: 68px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .back-link {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            gap: 8px;
            color: var(--text);
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary-dark);
        }

        .table-badge {
            flex-shrink: 0;
            padding: 8px 13px;
            border: 1px solid #fde68a;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 900;
        }

        .page-header {
            padding: 32px 0 23px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        .page-header h1 {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.15;
        }

        .page-header p:last-child {
            max-width: 650px;
            margin: 9px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .alert {
            margin-bottom: 20px;
            padding: 14px 16px;
            border: 1px solid #fecaca;
            border-radius: 14px;
            background: var(--danger-soft);
            color: #991b1b;
            font-size: 13px;
            line-height: 1.55;
        }

        .alert strong {
            display: block;
            margin-bottom: 5px;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(310px, 360px);
            align-items: start;
            gap: 22px;
            padding-bottom: 60px;
        }

        .form-column {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 18px;
        }

        .section-card,
        .summary-card {
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .section-card {
            padding: 22px;
        }

        .section-heading {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }

        .section-number {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            place-items: center;
            border-radius: 10px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 900;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 19px;
        }

        .section-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #374151;
            font-size: 12px;
            font-weight: 800;
        }

        .required {
            color: var(--danger);
        }

        .form-input,
        .form-textarea {
            display: block;
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: #ffffff;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input {
            min-height: 48px;
            padding: 11px 13px;
        }

        .form-textarea {
            min-height: 100px;
            padding: 12px 13px;
            resize: vertical;
        }

        .form-input:focus,
        .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.13);
        }

        .form-input.is-invalid,
        .form-textarea.is-invalid {
            border-color: var(--danger);
        }

        .field-error {
            margin: 6px 0 0;
            color: var(--danger);
            font-size: 11px;
            font-weight: 700;
        }

        .field-help {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.5;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
        }

        .payment-option {
            position: relative;
            min-width: 0;
            cursor: pointer;
        }

        .payment-option input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
        }

        .payment-card {
            display: flex;
            min-height: 120px;
            height: 100%;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #ffffff;
            flex-direction: column;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }

        .payment-option:hover .payment-card {
            border-color: #fbbf24;
            transform: translateY(-1px);
        }

        .payment-option input:checked + .payment-card {
            border-color: var(--primary);
            background: var(--primary-soft);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
        }

        .payment-icon {
            margin-bottom: 11px;
            font-size: 25px;
        }

        .payment-title {
            font-size: 14px;
            font-weight: 900;
        }

        .payment-description {
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.45;
        }

        .online-notice {
            margin-top: 13px;
            padding: 12px 14px;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 11px;
            line-height: 1.55;
        }

        .confirmation-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #374151;
            cursor: pointer;
            font-size: 12px;
            line-height: 1.6;
        }

        .confirmation-label input {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            margin-top: 1px;
            accent-color: var(--primary);
        }

        .summary-card {
            position: sticky;
            top: 88px;
            min-width: 0;
            overflow: hidden;
        }

        .summary-header {
            padding: 20px 21px 15px;
            border-bottom: 1px solid var(--border);
        }

        .summary-header h2 {
            margin: 0;
            font-size: 19px;
        }

        .summary-header p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .summary-items {
            display: flex;
            max-height: 335px;
            overflow-y: auto;
            padding: 7px 20px;
            flex-direction: column;
        }

        .summary-item {
            display: grid;
            grid-template-columns: 54px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary-item:last-child {
            border-bottom: 0;
        }

        .summary-image,
        .summary-placeholder {
            width: 54px;
            height: 54px;
            border-radius: 11px;
            background: #fef3c7;
        }

        .summary-image {
            display: block;
            object-fit: cover;
        }

        .summary-placeholder {
            display: grid;
            place-items: center;
            font-size: 22px;
        }

        .summary-info {
            min-width: 0;
        }

        .summary-name {
            overflow: hidden;
            margin: 0;
            font-size: 12px;
            font-weight: 900;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .summary-meta {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 10px;
        }

        .summary-price {
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .summary-footer {
            padding: 17px 20px 20px;
            border-top: 1px solid var(--border);
        }

        .price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 7px 0;
            color: var(--muted);
            font-size: 12px;
        }

        .price-row strong {
            color: var(--text);
        }

        .total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-top: 8px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            font-size: 18px;
            font-weight: 900;
        }

        .total-row span:last-child {
            color: var(--primary-dark);
            text-align: right;
        }

        .submit-button {
            display: flex;
            width: 100%;
            min-height: 52px;
            align-items: center;
            justify-content: center;
            margin-top: 18px;
            padding: 13px 16px;
            border: 0;
            border-radius: 14px;
            background: var(--coffee);
            color: #ffffff;
            cursor: pointer;
            font-weight: 900;
            transition: opacity 0.2s, transform 0.2s;
        }

        .submit-button:hover {
            transform: translateY(-1px);
        }

        .submit-button:disabled {
            cursor: wait;
            opacity: 0.65;
            transform: none;
        }

        .submit-note {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.5;
            text-align: center;
        }

        @media (max-width: 860px) {
            .container {
                width: min(720px, calc(100% - 28px));
            }

            .checkout-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .summary-card {
                position: static;
            }

            .summary-items {
                max-height: none;
            }
        }

        @media (max-width: 580px) {
            .container {
                width: calc(100% - 20px);
            }

            .topbar-content {
                min-height: 62px;
            }

            .back-link {
                font-size: 13px;
            }

            .table-badge {
                padding: 7px 10px;
                font-size: 11px;
            }

            .page-header {
                padding: 24px 2px 18px;
            }

            .page-header h1 {
                font-size: 28px;
            }

            .section-card {
                padding: 17px;
                border-radius: 18px;
            }

            .form-grid,
            .payment-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .payment-card {
                min-height: 0;
            }

            .summary-card {
                border-radius: 18px;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="topbar-content container">
            <a
                class="back-link"
                href="{{ route('customer.cart.show', ['token' => $cafeTable->qr_token]) }}"
            >
                <span>←</span>
                <span>Kembali ke Pesanan</span>
            </a>

            <span class="table-badge">
                Meja {{ $cafeTable->table_number }}
            </span>
        </div>
    </header>

    <main class="container">
        <section class="page-header">
            <p class="eyebrow">Second Cafe</p>
            <h1>Checkout Pesanan</h1>
            <p>
                Lengkapi informasi pemesan dan pilih metode pembayaran sebelum pesanan dikirim ke sistem.
            </p>
        </section>

        @if (session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert">
                <strong>Checkout belum dapat diproses.</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            id="checkout-form"
            class="checkout-layout"
            method="POST"
            action="{{ route('customer.checkout.store', ['token' => $cafeTable->qr_token]) }}"
        >
            @csrf

            <input
                type="hidden"
                name="checkout_token"
                value="{{ $checkoutToken }}"
            >

            <div class="form-column">
                <section class="section-card">
                    <div class="section-heading">
                        <span class="section-number">1</span>

                        <div>
                            <h2>Informasi Pemesan</h2>
                            <p>Data ini digunakan untuk identitas pesanan dan pengiriman struk.</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label" for="customer_name">
                                Nama Lengkap <span class="required">*</span>
                            </label>

                            <input
                                id="customer_name"
                                class="form-input @error('customer_name') is-invalid @enderror"
                                type="text"
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                maxlength="100"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                required
                            >

                            @error('customer_name')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="customer_phone">
                                Nomor HP <span class="required">*</span>
                            </label>

                            <input
                                id="customer_phone"
                                class="form-input @error('customer_phone') is-invalid @enderror"
                                type="tel"
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                maxlength="20"
                                placeholder="Contoh: 081234567890"
                                inputmode="tel"
                                autocomplete="tel"
                                required
                            >

                            @error('customer_phone')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="customer_email">
                                Email Struk <span class="required">*</span>
                            </label>

                            <input
                                id="customer_email"
                                class="form-input @error('customer_email') is-invalid @enderror"
                                type="email"
                                name="customer_email"
                                value="{{ old('customer_email') }}"
                                maxlength="150"
                                placeholder="nama@email.com"
                                autocomplete="email"
                                required
                            >

                            @error('customer_email')
                                <p class="field-error">{{ $message }}</p>
                            @enderror

                            <p class="field-help">Bukti transaksi akan dikirim ke alamat email ini.</p>
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <div class="section-heading">
                        <span class="section-number">2</span>

                        <div>
                            <h2>Metode Pembayaran</h2>
                            <p>Pilih cara pembayaran yang akan digunakan untuk pesanan ini.</p>
                        </div>
                    </div>

                    <div class="payment-grid">
                        <label class="payment-option">
                            <input
                                type="radio"
                                name="payment_method"
                                value="cashier"
                                @checked(old('payment_method', 'cashier') === 'cashier')
                                required
                            >

                            <span class="payment-card">
                                <span class="payment-icon">💵</span>
                                <span class="payment-title">Bayar di Kasir</span>
                                <span class="payment-description">
                                    Bayarkan pesanan langsung kepada petugas kasir.
                                </span>
                            </span>
                        </label>

                        <label class="payment-option">
                            <input
                                type="radio"
                                name="payment_method"
                                value="online"
                                @checked(old('payment_method') === 'online')
                                required
                            >

                            <span class="payment-card">
                                <span class="payment-icon">📱</span>
                                <span class="payment-title">Pembayaran Online</span>
                                <span class="payment-description">
                                    Lanjutkan ke alur QRIS atau transfer bank.
                                </span>
                            </span>
                        </label>
                    </div>

                    @error('payment_method')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                    <div id="online-notice" class="online-notice" hidden>
                        Integrasi pembayaran online masih dalam tahap pengembangan. Pesanan tetap dicatat dengan status menunggu pembayaran.
                    </div>
                </section>

                <section class="section-card">
                    <div class="section-heading">
                        <span class="section-number">3</span>

                        <div>
                            <h2>Konfirmasi Pesanan</h2>
                            <p>Tambahkan catatan umum dan konfirmasikan kebenaran pesanan.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">
                            Catatan Umum
                        </label>

                        <textarea
                            id="notes"
                            class="form-textarea @error('notes') is-invalid @enderror"
                            name="notes"
                            maxlength="500"
                            placeholder="Contoh: pesanan diantar bersamaan..."
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="margin-top: 17px;">
                        <label class="confirmation-label">
                            <input
                                type="checkbox"
                                name="confirmation"
                                value="1"
                                @checked(old('confirmation'))
                                required
                            >

                            <span>
                                Saya telah memeriksa menu, jumlah, catatan, nomor meja, dan total pembayaran pesanan ini.
                            </span>
                        </label>

                        @error('confirmation')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </section>
            </div>

            <aside class="summary-card">
                <div class="summary-header">
                    <h2>Ringkasan Pesanan</h2>
                    <p>{{ $cart['total_quantity'] }} item • Meja {{ $cafeTable->table_number }}</p>
                </div>

                <div class="summary-items">
                    @foreach ($cart['items'] as $item)
                        <div class="summary-item">
                            @if ($item['image_url'])
                                <img
                                    class="summary-image"
                                    src="{{ $item['image_url'] }}"
                                    alt="{{ $item['name'] }}"
                                >
                            @else
                                <div class="summary-placeholder">🍽</div>
                            @endif

                            <div class="summary-info">
                                <p class="summary-name">{{ $item['name'] }}</p>
                                <p class="summary-meta">
                                    {{ $item['quantity'] }} × Rp{{ number_format($item['unit_price'], 0, ',', '.') }}
                                </p>
                            </div>

                            <span class="summary-price">
                                Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="summary-footer">
                    <div class="price-row">
                        <span>Subtotal</span>
                        <strong>Rp{{ number_format($cart['subtotal'], 0, ',', '.') }}</strong>
                    </div>

                    <div class="price-row">
                        <span>Biaya layanan</span>
                        <strong>Rp0</strong>
                    </div>

                    <div class="total-row">
                        <span>Total</span>
                        <span>Rp{{ number_format($cart['total_amount'], 0, ',', '.') }}</span>
                    </div>

                    <button id="submit-button" class="submit-button" type="submit">
                        <span id="submit-label">Buat Pesanan</span>
                    </button>

                    <p class="submit-note">
                        Pesanan tidak dapat diubah dari halaman checkout setelah berhasil dibuat.
                    </p>
                </div>
            </aside>
        </form>
    </main>

    <script>
        const form = document.getElementById('checkout-form');
        const submitButton = document.getElementById('submit-button');
        const submitLabel = document.getElementById('submit-label');
        const onlineNotice = document.getElementById('online-notice');
        const paymentInputs = document.querySelectorAll(
            'input[name="payment_method"]'
        );

        function updatePaymentDisplay() {
            const selected = document.querySelector(
                'input[name="payment_method"]:checked'
            );

            const isOnline = selected?.value === 'online';

            onlineNotice.hidden = !isOnline;
            submitLabel.textContent = isOnline
                ? 'Lanjut Pembayaran'
                : 'Buat Pesanan';
        }

        paymentInputs.forEach((input) => {
            input.addEventListener('change', updatePaymentDisplay);
        });

        form.addEventListener('submit', () => {
            submitButton.disabled = true;
            submitLabel.textContent = 'Memproses Pesanan...';
        });

        updatePaymentDisplay();
    </script>
</body>

</html>