<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <meta content="{{ csrf_token() }}" name="csrf-token">

    <title>Tinjau Pesanan - Second Cafe</title>

    <style>
        :root {
            --primary: #f59e0b;
            --primary-light: #fffbeb;
            --primary-dark: #b45309;
            --coffee: #292018;
            --background: #f8fafc;
            --surface: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --success: #15803d;
            --shadow: 0 12px 32px rgba(15, 23, 42, 0.07);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background: var(--background);
            color: var(--text);
            font-family: Inter, Arial, sans-serif;
        }

        button,
        input,
        textarea {
            font: inherit;
        }

        button {
            -webkit-tap-highlight-color: transparent;
        }

        .container {
            width: min(1080px, calc(100% - 32px));
            margin-inline: auto;
        }

        /*
        |--------------------------------------------------------------------------
        | Topbar
        |--------------------------------------------------------------------------
        */

        .topbar {
            position: sticky;
            z-index: 50;
            top: 0;
            border-bottom: 1px solid rgba(229, 231, 235, 0.9);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
        }

        .topbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 68px;
            gap: 16px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            color: var(--text);
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary-dark);
        }

        .back-icon {
            font-size: 20px;
            line-height: 1;
        }

        .table-badge {
            flex-shrink: 0;
            padding: 8px 13px;
            border: 1px solid #fde68a;
            border-radius: 999px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 900;
        }

        /*
        |--------------------------------------------------------------------------
        | Page header
        |--------------------------------------------------------------------------
        */

        .page-header {
            padding: 32px 0 24px;
        }

        .page-header h1 {
            margin: 0;
            font-size: clamp(26px, 4vw, 38px);
            line-height: 1.15;
        }

        .page-header p {
            max-width: 600px;
            margin: 9px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */

        .cart-layout {
            display: grid;
            grid-template-columns:
                minmax(0, 1fr) minmax(280px, 320px);
            align-items: start;
            gap: 22px;
            padding-bottom: 60px;
        }

        .cart-items {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 16px;
        }

        /*
        |--------------------------------------------------------------------------
        | Item card
        |--------------------------------------------------------------------------
        */

        .cart-item {
            display: grid;
            grid-template-columns: 170px minmax(0, 1fr);
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .item-media {
            position: relative;
            min-width: 0;
            min-height: 225px;
            overflow: hidden;
            background: #fef3c7;
        }

        .item-image {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-placeholder {
            display: grid;
            width: 100%;
            height: 100%;
            min-height: 225px;
            place-items: center;
            background:
                linear-gradient(135deg,
                    #fffbeb,
                    #fef3c7);
            font-size: 42px;
        }

        .item-content {
            display: flex;
            min-width: 0;
            padding: 20px;
            flex-direction: column;
        }

        .item-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            min-width: 0;
            gap: 16px;
        }

        .item-name {
            min-width: 0;
            margin: 0;
            font-size: 18px;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .item-price {
            flex: 0 0 auto;
            color: var(--primary-dark);
            font-size: 16px;
            font-weight: 900;
            white-space: nowrap;
        }

        .options-list {
            display: flex;
            margin-top: 10px;
            flex-wrap: wrap;
            gap: 7px;
        }

        .option-badge {
            padding: 5px 9px;
            border: 1px solid #fde68a;
            border-radius: 999px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 700;
        }

        .notes-label {
            display: block;
            margin: 16px 0 7px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .notes-input {
            display: block;
            width: 100%;
            min-height: 72px;
            padding: 11px 12px;
            resize: vertical;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: #ffffff;
            color: var(--text);
            outline: none;
            transition:
                border-color 0.2s,
                box-shadow 0.2s;
        }

        .notes-input::placeholder {
            color: #9ca3af;
        }

        .notes-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.13);
        }

        .item-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 16px;
            gap: 14px;
        }

        /*
        |--------------------------------------------------------------------------
        | Quantity
        |--------------------------------------------------------------------------
        */

        .quantity-control {
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
            gap: 8px;
        }

        .quantity-button {
            display: grid;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: 50%;
            background: #ffffff;
            color: var(--text);
            cursor: pointer;
            font-size: 20px;
            font-weight: 700;
            transition:
                border-color 0.2s,
                background 0.2s,
                transform 0.2s;
        }

        .quantity-button:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .quantity-button:active {
            transform: scale(0.94);
        }

        .quantity-input {
            width: 38px;
            border: 0;
            background: transparent;
            color: var(--text);
            text-align: center;
            font-weight: 900;
            appearance: textfield;
        }

        .quantity-input::-webkit-inner-spin-button,
        .quantity-input::-webkit-outer-spin-button {
            margin: 0;
            appearance: none;
        }

        /*
        |--------------------------------------------------------------------------
        | Actions
        |--------------------------------------------------------------------------
        */

        .item-actions {
            display: flex;
            min-width: 0;
            gap: 8px;
        }

        .save-button,
        .delete-button {
            min-height: 40px;
            padding: 9px 14px;
            border: 0;
            border-radius: 11px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
            transition:
                opacity 0.2s,
                transform 0.2s;
        }

        .save-button {
            background: var(--primary);
            color: var(--coffee);
        }

        .delete-button {
            background: var(--danger-light);
            color: var(--danger);
        }

        .save-button:hover,
        .delete-button:hover {
            transform: translateY(-1px);
        }

        .save-button:disabled,
        .delete-button:disabled {
            cursor: wait;
            opacity: 0.55;
            transform: none;
        }

        .item-actions {
            display: flex;
            min-width: 0;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .save-status {
            min-height: 38px;
            padding: 8px 4px;
            border: 0;
            background: transparent;
            color: var(--muted);
            cursor: default;
            font-size: 11px;
            font-weight: 800;
            opacity: 1;
            white-space: nowrap;
        }

        .save-status.is-pending {
            color: var(--primary-dark);
        }

        .save-status.is-saving {
            color: var(--primary-dark);
        }

        .save-status.is-saved {
            color: var(--success);
        }

        .save-status.is-error {
            padding-inline: 9px;
            border-radius: 9px;
            background: var(--danger-light);
            color: var(--danger);
            cursor: pointer;
        }

        .save-status.is-error:not(:disabled):hover {
            background: #fecaca;
        }

        .delete-button {
            min-height: 40px;
            padding: 9px 14px;
            border: 0;
            border-radius: 11px;
            background: var(--danger-light);
            color: var(--danger);
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
        }

        .delete-button:disabled {
            cursor: wait;
            opacity: 0.55;
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        .summary-card {
            position: sticky;
            top: 88px;
            min-width: 0;
            padding: 22px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .summary-card h2 {
            margin: 0 0 17px;
            font-size: 20px;
        }

        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            color: var(--muted);
            font-size: 13px;
        }

        .summary-row strong {
            color: var(--text);
            text-align: right;
        }

        .summary-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 8px;
            padding-top: 17px;
            border-top: 1px solid var(--border);
            font-size: 18px;
            font-weight: 900;
        }

        .summary-total span:last-child {
            color: var(--primary-dark);
            text-align: right;
        }

        .checkout-button {
            display: flex;
            width: 100%;
            min-height: 50px;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            padding: 13px;
            border: 0;
            border-radius: 14px;
            background: var(--coffee);
            color: #ffffff;
            cursor: pointer;
            font-weight: 900;
            text-align: center;
            text-decoration: none;
        }

        .checkout-button:hover {
            background: #17120e;
        }

        .checkout-note {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.5;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | Empty state
        |--------------------------------------------------------------------------
        */

        .empty-state {
            padding: 60px 20px;
            border: 1px dashed #d1d5db;
            border-radius: 22px;
            background: #ffffff;
            text-align: center;
        }

        .empty-icon {
            font-size: 44px;
        }

        .empty-state h2 {
            margin: 12px 0 7px;
        }

        .empty-state p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.6;
        }

        .menu-link {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            padding: 11px 17px;
            border-radius: 12px;
            background: var(--primary);
            color: var(--coffee);
            font-weight: 900;
            text-decoration: none;
        }

        /*
        |--------------------------------------------------------------------------
        | Tablet
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {
            .container {
                width: min(760px, calc(100% - 32px));
            }

            .cart-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .summary-card {
                position: static;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Handphone
        |--------------------------------------------------------------------------
        */

        @media (max-width: 640px) {
            .container {
                width: calc(100% - 24px);
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
                font-size: 27px;
            }

            .page-header p {
                font-size: 13px;
            }

            .cart-layout {
                gap: 18px;
                padding-bottom: 32px;
            }

            .cart-item {
                grid-template-columns: minmax(0, 1fr);
                border-radius: 19px;
            }

            .item-media {
                min-height: 0;
                aspect-ratio: 16 / 9;
            }

            .image-placeholder {
                min-height: 0;
                aspect-ratio: 16 / 9;
            }

            .item-content {
                padding: 16px;
            }

            .item-footer {
                flex-wrap: wrap;
            }

            .summary-card {
                padding: 19px;
                border-radius: 19px;
            }

            .checkout-button {
                min-height: 52px;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Handphone kecil
        |--------------------------------------------------------------------------
        */

        @media (max-width: 420px) {
            .item-actions {
                width: 100%;
                justify-content: space-between;
            }

            .save-status {
                min-width: 0;
                text-align: left;
                white-space: normal;
            }

            .delete-button {
                min-width: 90px;
                min-height: 44px;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="topbar-content container">
            <a class="back-link"
                href="{{ route('customer.menu', [
                    'token' => $cafeTable->qr_token,
                ]) }}">
                <span class="back-icon">←</span>
                <span>Tambah Menu</span>
            </a>

            <span class="table-badge">
                Meja {{ $cafeTable->table_number }}
            </span>
        </div>
    </header>

    <main class="container">
        <section class="page-header">
            <h1>Tinjau Pesanan</h1>

            <p>
                Periksa jumlah dan catatan setiap menu
                sebelum melanjutkan pembayaran.
            </p>
        </section>

        @if ($cart['total_quantity'] > 0)
            <div class="cart-layout">
                <section class="cart-items">
                    @foreach ($cart['items'] as $item)
                        <article class="cart-item"
                            data-delete-url="{{ route('customer.cart.destroy', [
                                'token' => $cafeTable->qr_token,
                                'lineId' => $item['line_id'],
                            ]) }}"
                            data-unit-price="{{ $item['unit_price'] }}"
                            data-update-url="{{ route('customer.cart.update', [
                                'token' => $cafeTable->qr_token,
                                'lineId' => $item['line_id'],
                            ]) }}">
                            <div class="item-media">
                                @if ($item['image_url'])
                                    <img alt="{{ $item['name'] }}" class="item-image" loading="lazy"
                                        src="{{ $item['image_url'] }}">
                                @else
                                    <div class="image-placeholder">
                                        🍽
                                    </div>
                                @endif
                            </div>

                            <div class="item-content">
                                <div class="item-heading">
                                    <h2 class="item-name">
                                        {{ $item['name'] }}
                                    </h2>

                                    <span class="item-price">
                                        Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </span>
                                </div>

                                @if (!empty($item['selected_options']))
                                    <div class="options-list">
                                        @foreach ($item['selected_options'] as $option)
                                            <span class="option-badge">
                                                {{ $option['group'] }}:
                                                {{ $option['option'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <label class="notes-label" for="notes-{{ $item['line_id'] }}">
                                    Catatan Pesanan
                                </label>

                                <textarea class="notes-input" id="notes-{{ $item['line_id'] }}" maxlength="255"
                                    placeholder="Contoh: tidak pedas, sedikit gula...">{{ $item['notes'] }}</textarea>

                                <div class="item-footer">
                                    <div class="quantity-control">
                                        <button aria-label="Kurangi jumlah" class="quantity-button decrease"
                                            type="button">
                                            −
                                        </button>

                                        <input aria-label="Jumlah pesanan" class="quantity-input" max="99"
                                            min="1" readonly type="number" value="{{ $item['quantity'] }}">

                                        <button aria-label="Tambah jumlah" class="quantity-button increase"
                                            type="button">
                                            +
                                        </button>
                                    </div>

                                    <div class="item-actions">
                                        <button aria-live="polite" class="save-status is-saved" disabled type="button">
                                            ✓ Tersimpan otomatis
                                        </button>

                                        <button class="delete-button" type="button">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <aside class="summary-card">
                    <h2>Ringkasan Pesanan</h2>

                    <div class="summary-row">
                        <span>Jumlah Menu</span>

                        <strong id="summary-quantity">
                            {{ $cart['total_quantity'] }} item
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>Nomor Meja</span>

                        <strong>
                            {{ $cafeTable->table_number }}
                        </strong>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>

                        <span id="summary-total">
                            Rp{{ number_format($cart['total_amount'], 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="checkout-button">
                        Lanjut ke Checkout
                    </div>

                    <p class="checkout-note">
                        Pastikan jumlah dan catatan pesanan
                        sudah sesuai sebelum melanjutkan.
                    </p>
                </aside>
            </div>
        @else
            <section class="empty-state">
                <div class="empty-icon">🛒</div>

                <h2>Keranjang masih kosong</h2>

                <p>
                    Silakan pilih makanan atau minuman
                    terlebih dahulu.
                </p>

                <a class="menu-link"
                    href="{{ route('customer.menu', [
                        'token' => $cafeTable->qr_token,
                    ]) }}">
                    Lihat Menu
                </a>
            </section>
        @endif
    </main>

    <script>
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const rupiahFormatter = new Intl.NumberFormat(
            'id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }
        );

        const summaryQuantity = document.getElementById(
            'summary-quantity'
        );

        const summaryTotal = document.getElementById(
            'summary-total'
        );

        /**
         * Menghitung kembali subtotal setiap menu dan
         * total keseluruhan tanpa reload halaman.
         */
        function refreshCartTotals() {
            let totalQuantity = 0;
            let totalAmount = 0;

            document
                .querySelectorAll('.cart-item')
                .forEach((item) => {
                    const quantity = Number(
                        item.querySelector(
                            '.quantity-input'
                        ).value
                    );

                    const unitPrice = Number(
                        item.dataset.unitPrice
                    );

                    const subtotal = unitPrice * quantity;

                    totalQuantity += quantity;
                    totalAmount += subtotal;

                    item.querySelector(
                        '.item-price'
                    ).textContent = rupiahFormatter.format(
                        subtotal
                    );
                });

            if (summaryQuantity) {
                summaryQuantity.textContent =
                    `${totalQuantity} item`;
            }

            if (summaryTotal) {
                summaryTotal.textContent =
                    rupiahFormatter.format(totalAmount);
            }
        }

        document
            .querySelectorAll('.cart-item')
            .forEach((item) => {
                const quantityInput = item.querySelector(
                    '.quantity-input'
                );

                const notesInput = item.querySelector(
                    '.notes-input'
                );

                const increaseButton = item.querySelector(
                    '.increase'
                );

                const decreaseButton = item.querySelector(
                    '.decrease'
                );

                const saveStatus = item.querySelector(
                    '.save-status'
                );

                const deleteButton = item.querySelector(
                    '.delete-button'
                );

                let savedQuantity = Number(
                    quantityInput.value
                );

                let savedNotes = notesInput.value;

                let saveTimer = null;
                let savedStatusTimer = null;
                let isSaving = false;
                let saveAgain = false;
                let itemDeleted = false;

                function hasChanges() {
                    return (
                        Number(quantityInput.value) !==
                        savedQuantity ||
                        notesInput.value !== savedNotes
                    );
                }

                function setStatus(type, message) {
                    clearTimeout(savedStatusTimer);

                    saveStatus.className =
                        `save-status is-${type}`;

                    saveStatus.textContent = message;

                    /*
                     * Tombol status hanya dapat diklik
                     * apabila proses penyimpanan gagal.
                     */
                    saveStatus.disabled = type !== 'error';
                }

                function scheduleSave(delay = 800) {
                    clearTimeout(saveTimer);

                    if (!hasChanges()) {
                        return;
                    }

                    setStatus(
                        'pending',
                        'Perubahan belum disimpan...'
                    );

                    saveTimer = setTimeout(() => {
                        persistChanges();
                    }, delay);
                }

                async function persistChanges() {
                    clearTimeout(saveTimer);

                    if (itemDeleted) {
                        return;
                    }

                    if (isSaving) {
                        saveAgain = true;

                        return;
                    }

                    if (!hasChanges()) {
                        setStatus(
                            'saved',
                            '✓ Tersimpan otomatis'
                        );

                        return;
                    }

                    isSaving = true;
                    saveAgain = false;

                    const payload = {
                        quantity: Number(
                            quantityInput.value
                        ),
                        notes: notesInput.value,
                    };

                    setStatus(
                        'saving',
                        'Menyimpan...'
                    );

                    let saveFailed = false;

                    try {
                        const response = await fetch(
                            item.dataset.updateUrl, {
                                method: 'PATCH',
                                keepalive: true,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify(payload),
                            }
                        );

                        const result = await response
                            .json()
                            .catch(() => ({}));

                        if (!response.ok) {
                            const validationMessage =
                                result.errors ?
                                Object.values(
                                    result.errors
                                ).flat()[0] :
                                null;

                            throw new Error(
                                validationMessage ||
                                result.message ||
                                'Perubahan gagal disimpan.'
                            );
                        }

                        savedQuantity = payload.quantity;
                        savedNotes = payload.notes;

                        setStatus(
                            'saved',
                            '✓ Tersimpan'
                        );

                        savedStatusTimer = setTimeout(() => {
                            setStatus(
                                'saved',
                                '✓ Tersimpan otomatis'
                            );
                        }, 1800);
                    } catch (error) {
                        saveFailed = true;

                        setStatus(
                            'error',
                            '↻ Gagal, coba lagi'
                        );

                        console.error(error);
                    } finally {
                        isSaving = false;

                        /*
                         * Jika pengguna kembali mengubah data
                         * selama request berjalan, simpan data
                         * terbaru setelah request selesai.
                         */
                        if (
                            !saveFailed &&
                            (
                                saveAgain ||
                                hasChanges()
                            )
                        ) {
                            saveAgain = false;

                            persistChanges();
                        }
                    }
                }

                increaseButton.addEventListener(
                    'click',
                    () => {
                        const quantity = Number(
                            quantityInput.value
                        );

                        if (quantity >= 99) {
                            return;
                        }

                        quantityInput.value = quantity + 1;

                        refreshCartTotals();

                        /*
                         * Debounce singkat agar klik berulang
                         * tidak mengirim terlalu banyak request.
                         */
                        scheduleSave(250);
                    }
                );

                decreaseButton.addEventListener(
                    'click',
                    () => {
                        const quantity = Number(
                            quantityInput.value
                        );

                        if (quantity <= 1) {
                            return;
                        }

                        quantityInput.value = quantity - 1;

                        refreshCartTotals();
                        scheduleSave(250);
                    }
                );

                /*
                 * Catatan disimpan setelah pengguna berhenti
                 * mengetik selama 800 milidetik.
                 */
                notesInput.addEventListener(
                    'input',
                    () => {
                        scheduleSave(800);
                    }
                );

                /*
                 * Simpan langsung ketika pengguna keluar
                 * dari kolom catatan.
                 */
                notesInput.addEventListener(
                    'blur',
                    () => {
                        if (hasChanges()) {
                            scheduleSave(0);
                        }
                    }
                );

                /*
                 * Tombol status berubah menjadi tombol retry
                 * hanya ketika penyimpanan gagal.
                 */
                saveStatus.addEventListener(
                    'click',
                    () => {
                        if (
                            saveStatus.classList.contains(
                                'is-error'
                            )
                        ) {
                            persistChanges();
                        }
                    }
                );

                deleteButton.addEventListener(
                    'click',
                    async function() {
                        const confirmed = confirm(
                            'Hapus menu ini dari keranjang?'
                        );

                        if (!confirmed) {
                            return;
                        }

                        clearTimeout(saveTimer);

                        itemDeleted = true;
                        this.disabled = true;
                        this.textContent = 'Menghapus...';

                        try {
                            const response = await fetch(
                                item.dataset.deleteUrl, {
                                    method: 'DELETE',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                    },
                                }
                            );

                            if (!response.ok) {
                                throw new Error(
                                    'Menu gagal dihapus.'
                                );
                            }

                            item.remove();
                            refreshCartTotals();

                            /*
                             * Reload hanya jika seluruh item
                             * telah dihapus agar empty state muncul.
                             */
                            if (
                                document.querySelectorAll(
                                    '.cart-item'
                                ).length === 0
                            ) {
                                window.location.reload();
                            }
                        } catch (error) {
                            itemDeleted = false;

                            alert(error.message);

                            this.disabled = false;
                            this.textContent = 'Hapus';
                        }
                    }
                );
            });

        refreshCartTotals();
    </script>
</body>

</html>
