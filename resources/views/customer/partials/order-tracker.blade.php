@php
    $terminalStatuses = ['selesai', 'dibatalkan'];

    $unpaidDisplaySeconds = \App\Support\CustomerOrderTracker::UNPAID_DISPLAY_MINUTES * 60;

    $paidDisplaySeconds = \App\Support\CustomerOrderTracker::PAID_DISPLAY_MINUTES * 60;

    $activeOrdersCount = $trackedOrders->whereNotIn('status', $terminalStatuses)->count();
@endphp

@if ($trackedOrders->isNotEmpty())
    <section class="sc-order-tracker" data-order-tracker>
        <div class="sc-order-tracker__header">
            <div>
                <span class="sc-order-tracker__eyebrow">
                    Pesanan pelanggan
                </span>

                <h2 class="sc-order-tracker__title">
                    Pesanan Saya

                    <span class="sc-order-tracker__count" data-active-order-count>
                        {{ $activeOrdersCount }}
                    </span>
                </h2>
            </div>

            <button class="sc-notification-button" data-enable-notifications type="button">
                Aktifkan Notifikasi
            </button>
        </div>

        <div class="sc-order-tracker__list">
            @foreach ($trackedOrders as $order)
                @php
                    $statusClass = match ($order->status) {
                        'menunggu_pembayaran' => 'status-menunggu_pembayaran',

                        'menunggu_verifikasi' => 'status-menunggu_verifikasi',

                        'diterima' => 'status-diterima',

                        'diproses' => 'status-diproses',

                        'siap' => 'status-siap',

                        'selesai' => 'status-selesai',

                        'dibatalkan' => 'status-dibatalkan',

                        default => 'status-default',
                    };

                    $statusMessage = match ($order->status) {
                        'menunggu_pembayaran' => 'Silakan lakukan pembayaran di kasir.',

                        'menunggu_verifikasi' => 'Pembayaran sedang diperiksa.',

                        'diterima' => 'Pesanan telah diterima oleh petugas.',

                        'diproses' => 'Pesanan sedang disiapkan.',

                        'siap' => 'Pesanan sudah siap.',

                        'selesai' => 'Pesanan telah diselesaikan.',

                        'dibatalkan' => 'Pesanan telah dibatalkan.',

                        default => 'Status pesanan sedang diperbarui.',
                    };

                    $trackingExpiresAt = \App\Support\CustomerOrderTracker::expiresAt($order);
                @endphp

                <article @class(['sc-order-card', $statusClass]) data-expires-at="{{ $trackingExpiresAt->timestamp }}"
                    data-order-card data-order-code="{{ $order->cashier_code }}"
                    data-payment-status-value="{{ $order->payment_status }}"
                    data-status-url="{{ route('customer.orders.status', [
                        'token' => $cafeTable->qr_token,
                        'order' => $order,
                    ]) }}"
                    data-status="{{ $order->status }}">
                    <div class="sc-order-card__main">
                        <div class="sc-order-card__top">
                            <div>
                                <span class="sc-order-card__label">
                                    Kode Pesanan
                                </span>

                                <strong class="sc-order-card__code">
                                    {{ $order->cashier_code }}
                                </strong>
                            </div>

                            <span class="sc-order-card__status" data-order-status>
                                {{ $order->status_label }}
                            </span>
                        </div>

                        <p class="sc-order-card__message" data-order-message>
                            {{ $statusMessage }}
                        </p>

                        <div class="sc-order-card__meta">
                            <span>
                                {{ $order->items_count }}
                                item
                            </span>

                            <span aria-hidden="true">•</span>

                            <strong>
                                Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="sc-order-card__footer">
                            <div>
                                <span class="sc-order-card__payment-label">
                                    Pembayaran
                                </span>

                                <span data-payment-status>
                                    {{ $order->payment_status_label }}
                                </span>
                            </div>

                            <a class="sc-order-card__link"
                                href="{{ route('customer.orders.success', [
                                    'token' => $cafeTable->qr_token,
                                
                                    'order' => $order,
                                ]) }}">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div aria-live="polite" class="sc-order-toast" data-order-toast role="status">
        <strong data-toast-title></strong>

        <span data-toast-message></span>
    </div>

    <style>
        .sc-order-tracker {
            width: 100%;
            margin: 18px 0 28px;
            padding: 18px;
            border: 1px solid #fde68a;
            border-radius: 20px;
            background:
                linear-gradient(145deg,
                    #fffbeb,
                    #ffffff);
            box-shadow:
                0 12px 30px rgba(120, 53, 15, 0.08);
        }

        .sc-order-tracker__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 15px;
        }

        .sc-order-tracker__eyebrow {
            display: block;
            margin-bottom: 3px;
            color: #b45309;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .sc-order-tracker__title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: #292018;
            font-size: 20px;
        }

        .sc-order-tracker__count {
            display: grid;
            min-width: 25px;
            height: 25px;
            place-items: center;
            padding: 0 7px;
            border-radius: 999px;
            background: #f59e0b;
            color: #292018;
            font-size: 11px;
        }

        .sc-notification-button {
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid #f59e0b;
            border-radius: 11px;
            background: #ffffff;
            color: #92400e;
            cursor: pointer;
            font-size: 11px;
            font-weight: 800;
        }

        .sc-notification-button[hidden] {
            display: none;
        }

        .sc-order-tracker__list {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .sc-order-card {
            position: relative;
            overflow: hidden;
            min-width: 0;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #ffffff;
            transition:
                border-color 0.2s,
                transform 0.2s;
        }

        .sc-order-card::before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 5px;
            background: #9ca3af;
            content: "";
        }

        .sc-order-card:hover {
            transform: translateY(-2px);
        }

        .sc-order-card__main {
            padding: 15px 15px 15px 19px;
        }

        .sc-order-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .sc-order-card__label,
        .sc-order-card__payment-label {
            display: block;
            margin-bottom: 3px;
            color: #6b7280;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .sc-order-card__code {
            color: #292018;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .sc-order-card__status {
            flex: 0 0 auto;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 9px;
            font-weight: 800;
        }

        .sc-order-card__message {
            margin: 12px 0 8px;
            color: #4b5563;
            font-size: 11px;
            line-height: 1.5;
        }

        .sc-order-card__meta {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 11px;
        }

        .sc-order-card__meta strong {
            color: #b45309;
        }

        .sc-order-card__footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-top: 13px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
            color: #374151;
            font-size: 10px;
        }

        .sc-order-card__link {
            flex: 0 0 auto;
            color: #b45309;
            font-size: 10px;
            font-weight: 800;
            text-decoration: none;
        }

        .status-menunggu_pembayaran::before {
            background: #f59e0b;
        }

        .status-menunggu_verifikasi::before {
            background: #3b82f6;
        }

        .status-diterima::before {
            background: #2563eb;
        }

        .status-diproses::before {
            background: #7c3aed;
        }

        .status-siap::before {
            background: #16a34a;
        }

        .status-selesai::before {
            background: #6b7280;
        }

        .status-dibatalkan::before {
            background: #dc2626;
        }

        .status-siap {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .status-dibatalkan {
            border-color: #fecaca;
            background: #fef2f2;
        }

        .sc-order-toast {
            position: fixed;
            z-index: 100;
            right: 18px;
            bottom: 18px;
            display: flex;
            width: min(360px, calc(100% - 36px));
            align-items: flex-start;
            flex-direction: column;
            gap: 4px;
            padding: 15px 17px;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            background: #ffffff;
            box-shadow:
                0 18px 45px rgba(15, 23, 42, 0.18);
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
            transition:
                opacity 0.25s,
                transform 0.25s;
        }

        .sc-order-toast.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .sc-order-toast strong {
            color: #166534;
            font-size: 13px;
        }

        .sc-order-toast span {
            color: #4b5563;
            font-size: 11px;
            line-height: 1.5;
        }

        @media (max-width: 680px) {
            .sc-order-tracker {
                padding: 15px;
                border-radius: 17px;
            }

            .sc-order-tracker__header {
                align-items: flex-start;
                flex-direction: column;
            }

            .sc-order-tracker__list {
                grid-template-columns: minmax(0, 1fr);
            }

            .sc-notification-button {
                width: 100%;
            }
        }

        @media (max-width: 380px) {

            .sc-order-card__top,
            .sc-order-card__footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <script>
        (() => {
            const tracker = document.querySelector(
                '[data-order-tracker]'
            );

            if (!tracker) {
                return;
            }

            const terminalStatuses = [
                'selesai',
                'dibatalkan',
            ];

            const unpaidDisplaySeconds =
                {{ (int) $unpaidDisplaySeconds }};

            const paidDisplaySeconds =
                {{ (int) $paidDisplaySeconds }};

            const statusClasses = [
                'status-menunggu_pembayaran',
                'status-menunggu_verifikasi',
                'status-diterima',
                'status-diproses',
                'status-siap',
                'status-selesai',
                'status-dibatalkan',
                'status-default',
            ];

            const toast = document.querySelector(
                '[data-order-toast]'
            );

            const notificationButton =
                tracker.querySelector(
                    '[data-enable-notifications]'
                );

            let toastTimeout = null;

            const expirationTimers =
                new WeakMap();

            function showToast(title, message) {
                if (!toast) {
                    return;
                }

                toast.querySelector(
                    '[data-toast-title]'
                ).textContent = title;

                toast.querySelector(
                    '[data-toast-message]'
                ).textContent = message;

                toast.classList.add('is-visible');

                window.clearTimeout(toastTimeout);

                toastTimeout = window.setTimeout(() => {
                    toast.classList.remove(
                        'is-visible'
                    );
                }, 6000);
            }

            function showBrowserNotification(
                code,
                status,
                message
            ) {
                if (
                    !('Notification' in window) ||
                    Notification.permission !==
                    'granted'
                ) {
                    return;
                }

                const importantStatuses = [
                    'diterima',
                    'siap',
                    'selesai',
                    'dibatalkan',
                ];

                if (
                    !importantStatuses.includes(status)
                ) {
                    return;
                }

                new Notification(
                    `Pesanan ${code}`, {
                        body: message,
                        tag: `second-cafe-${code}-${status}`,
                    }
                );
            }

            function updateActiveOrderCount() {
                const cards = tracker.querySelectorAll(
                    '[data-order-card]'
                );

                let activeCount = 0;

                cards.forEach((card) => {
                    if (
                        !terminalStatuses.includes(
                            card.dataset.status
                        )
                    ) {
                        activeCount++;
                    }
                });

                const counter = tracker.querySelector(
                    '[data-active-order-count]'
                );

                if (counter) {
                    counter.textContent = activeCount;
                }
            }

            function removeOrderCard(card) {
                const expirationTimer =
                    expirationTimers.get(card);

                if (expirationTimer) {
                    window.clearTimeout(
                        expirationTimer
                    );
                }

                expirationTimers.delete(card);
                card.remove();

                updateActiveOrderCount();

                const remainingCards =
                    tracker.querySelectorAll(
                        '[data-order-card]'
                    );

                if (remainingCards.length === 0) {
                    tracker.hidden = true;
                }
            }

            function scheduleOrderExpiration(
                card
            ) {
                const previousTimer =
                    expirationTimers.get(card);

                if (previousTimer) {
                    window.clearTimeout(
                        previousTimer
                    );

                    expirationTimers.delete(card);
                }

                let expiresAt = Number(
                    card.dataset.expiresAt ?? 0
                );

                /*
                 * Fallback digunakan jika endpoint status belum
                 * mengirim tracking_expires_at_timestamp.
                 */
                if (expiresAt < 1) {
                    const isPaid =
                        card.dataset
                        .paymentStatusValue ===
                        'paid';

                    expiresAt =
                        Math.floor(Date.now() / 1000) +
                        (
                            isPaid ?
                            paidDisplaySeconds :
                            unpaidDisplaySeconds
                        );

                    card.dataset.expiresAt =
                        expiresAt;
                }

                const delay = (
                    expiresAt -
                    Math.floor(Date.now() / 1000)
                ) * 1000;

                if (delay <= 0) {
                    removeOrderCard(card);

                    return;
                }

                const timer = window.setTimeout(
                    () => {
                        removeOrderCard(card);
                    },
                    delay
                );

                expirationTimers.set(
                    card,
                    timer
                );
            }

            function removeExpiredOrders() {
                const currentTimestamp =
                    Math.floor(Date.now() / 1000);

                tracker.querySelectorAll(
                    '[data-order-card]'
                ).forEach((card) => {
                    const expiresAt = Number(
                        card.dataset.expiresAt ?? 0
                    );

                    if (
                        expiresAt > 0 &&
                        expiresAt <= currentTimestamp
                    ) {
                        removeOrderCard(card);
                    }
                });
            }

            async function refreshOrder(card) {
                if (
                    terminalStatuses.includes(
                        card.dataset.status
                    )
                ) {
                    return;
                }

                try {
                    const response = await fetch(
                        card.dataset.statusUrl, {
                            method: 'GET',
                            headers: {
                                Accept: 'application/json',
                            },
                            cache: 'no-store',
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Status pesanan gagal diperiksa.'
                        );
                    }

                    const result =
                        await response.json();

                    if (
                        result.tracking_expired ===
                        true
                    ) {
                        removeOrderCard(card);

                        return;
                    }

                    const previousStatus =
                        card.dataset.status;

                    const currentStatus =
                        result.order_status;

                    if (!currentStatus) {
                        return;
                    }

                    card.dataset.status =
                        currentStatus;

                    statusClasses.forEach(
                        (className) => {
                            card.classList.remove(
                                className
                            );
                        }
                    );

                    card.classList.add(
                        `status-${currentStatus}`
                    );

                    card.dataset.paymentStatusValue =
                        result.payment_status ?? '';

                    const serverExpiresAt =
                        Number(
                            result
                            .tracking_expires_at_timestamp ??
                            0
                        );

                    const isPaid =
                        result.payment_status ===
                        'paid';

                    card.dataset.expiresAt =
                        serverExpiresAt > 0 ?
                        serverExpiresAt :
                        Math.floor(
                            Date.now() / 1000
                        ) +
                        (
                            isPaid ?
                            paidDisplaySeconds :
                            unpaidDisplaySeconds
                        );

                    const statusElement =
                        card.querySelector(
                            '[data-order-status]'
                        );

                    const messageElement =
                        card.querySelector(
                            '[data-order-message]'
                        );

                    const paymentElement =
                        card.querySelector(
                            '[data-payment-status]'
                        );

                    if (statusElement) {
                        statusElement.textContent =
                            result.order_status_label;
                    }

                    if (messageElement) {
                        messageElement.textContent =
                            result.message;
                    }

                    if (
                        paymentElement &&
                        result.payment_status_label
                    ) {
                        paymentElement.textContent =
                            result.payment_status_label;
                    }

                    if (
                        previousStatus !==
                        currentStatus
                    ) {
                        const code =
                            card.dataset.orderCode;

                        showToast(
                            `Pesanan ${code}`,
                            result.message
                        );

                        showBrowserNotification(
                            code,
                            currentStatus,
                            result.message
                        );
                    }

                    updateActiveOrderCount();
                    scheduleOrderExpiration(
                        card
                    );
                } catch (error) {
                    console.error(error);
                }
            }

            async function refreshAllOrders() {
                const cards = [
                    ...tracker.querySelectorAll(
                        '[data-order-card]'
                    ),
                ];

                const activeCards = cards.filter(
                    (card) =>
                    !terminalStatuses.includes(
                        card.dataset.status
                    )
                );

                await Promise.allSettled(
                    activeCards.map(refreshOrder)
                );
            }

            if (
                !('Notification' in window) ||
                Notification.permission ===
                'denied'
            ) {
                notificationButton.hidden = true;
            }

            if (
                'Notification' in window &&
                Notification.permission ===
                'granted'
            ) {
                notificationButton.textContent =
                    'Notifikasi Aktif';

                notificationButton.disabled = true;
            }

            notificationButton?.addEventListener(
                'click',
                async () => {
                    const permission =
                        await Notification
                        .requestPermission();

                    if (permission === 'granted') {
                        notificationButton.textContent =
                            'Notifikasi Aktif';

                        notificationButton.disabled =
                            true;

                        showToast(
                            'Notifikasi aktif',
                            'Perubahan status pesanan akan ditampilkan.'
                        );
                    } else {
                        notificationButton.hidden =
                            true;
                    }
                }
            );

            document.addEventListener(
                'visibilitychange',
                () => {
                    if (!document.hidden) {
                        removeExpiredOrders();
                        refreshAllOrders();
                    }
                }
            );

            window.addEventListener(
                'focus',
                () => {
                    removeExpiredOrders();
                    refreshAllOrders();
                }
            );

            tracker.querySelectorAll(
                '[data-order-card]'
            ).forEach(
                scheduleOrderExpiration
            );

            removeExpiredOrders();
            refreshAllOrders();

            window.setInterval(() => {
                removeExpiredOrders();

                if (!document.hidden) {
                    refreshAllOrders();
                }
            }, 10000);
        })();
    </script>
@endif
