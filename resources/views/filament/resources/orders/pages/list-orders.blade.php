<x-filament-panels::page>
    <div class="orders-page-shell" x-data="{
        soundEnabled: localStorage.getItem('orders:sound-enabled') === '1',
    
        audio: null,
    
        /*
         * Alpine akan menjalankan init() secara otomatis
         * saat halaman selesai dimuat.
         */
        init() {
            this.audio = new Audio(
                @js(asset('sounds/new-order-notification.mp3'))
            );
    
            this.audio.preload = 'auto';
            this.audio.volume = 0.85;
    
            /*
             * Memuat file suara lebih awal agar tidak terlambat
             * ketika pesanan baru masuk.
             */
            this.audio.load();
        },
    
        async toggleAlerts() {
            this.soundEnabled = !this.soundEnabled;
    
            localStorage.setItem(
                'orders:sound-enabled',
                this.soundEnabled ? '1' : '0'
            );
    
            if (!this.soundEnabled) {
                this.stopSound();
    
                return;
            }
    
            /*
             * Suara diputar ketika pengguna menekan tombol.
             * Interaksi ini membantu membuka izin audio browser.
             */
            await this.playSound();
    
            if (
                'Notification' in window &&
                window.Notification.permission === 'default'
            ) {
                try {
                    await window.Notification.requestPermission();
                } catch (error) {
                    console.warn(
                        'Izin notifikasi browser tidak dapat diminta:',
                        error
                    );
                }
            }
        },
    
        stopSound() {
            if (!this.audio) {
                return;
            }
    
            this.audio.pause();
            this.audio.currentTime = 0;
        },
    
        async playSound() {
            if (!this.soundEnabled || !this.audio) {
                return;
            }
    
            try {
                /*
                 * Menghentikan suara sebelumnya supaya suara
                 * notifikasi selalu dimulai dari awal.
                 */
                this.audio.pause();
                this.audio.currentTime = 0;
    
                await this.audio.play();
            } catch (error) {
                console.warn(
                    'Suara notifikasi belum diizinkan browser:',
                    error
                );
            }
        },
    
        async notify(detail) {
            await this.playSound();
    
            if (
                'Notification' in window &&
                window.Notification.permission === 'granted'
            ) {
                const notification = new window.Notification(
                    detail.title ?? 'Pesanan baru masuk!', {
                        body: detail.body ??
                            'Periksa daftar pesanan terbaru.',
    
                        icon: @js(asset('favicon.ico')),
    
                        tag: detail.orderCode ?
                            `new-order-${detail.orderCode}` :
                            'new-order-notification',
    
                        renotify: true,
                    }
                );
    
                notification.onclick = () => {
                    window.focus();
                    notification.close();
                };
            }
        },
    }" x-on:new-order-received.window="notify($event.detail)">
        {{-- ====================================================== --}}
        {{-- PANEL PEMANTAUAN PESANAN --}}
        {{-- ====================================================== --}}
        <section class="orders-monitor-card">
            {{-- Ilustrasi sebelah kiri --}}
            <div aria-hidden="true" class="orders-monitor-illustration">
                <div class="orders-monitor-circle orders-monitor-circle-large">
                    <div class="orders-monitor-circle orders-monitor-circle-small">
                        <x-filament::icon class="orders-monitor-main-icon" icon="heroicon-o-computer-desktop" />
                    </div>
                </div>

                <span class="orders-monitor-decoration decoration-one"></span>

                <span class="orders-monitor-decoration decoration-two"></span>

                <span class="orders-monitor-decoration decoration-three"></span>
            </div>

            {{-- Informasi utama --}}
            <div class="orders-monitor-content">
                <div class="orders-monitor-heading">
                    <h2>
                        Pemantauan Pesanan Otomatis
                    </h2>

                    <span class="orders-status-badge orders-status-active">
                        <x-filament::icon class="orders-status-icon" icon="heroicon-m-signal" />

                        Aktif
                    </span>
                </div>

                <p class="orders-monitor-description">
                    Pesanan baru diperiksa secara otomatis setiap 5
                    detik. Aktifkan suara agar kasir tidak melewatkan
                    pesanan yang masuk.
                </p>

                <div class="orders-monitor-actions">
                    {{-- Status suara aktif --}}
                    <span class="orders-sound-status orders-sound-status-active" x-cloak x-show="soundEnabled">
                        <x-filament::icon class="orders-action-icon" icon="heroicon-m-speaker-wave" />

                        Suara Aktif
                    </span>

                    {{-- Status suara tidak aktif --}}
                    <span class="orders-sound-status orders-sound-status-inactive" x-cloak x-show="! soundEnabled">
                        <x-filament::icon class="orders-action-icon" icon="heroicon-m-speaker-x-mark" />

                        Suara Nonaktif
                    </span>

                    {{-- Tombol pengaturan suara --}}
                    <button class="orders-sound-button" type="button" x-on:click="toggleAlerts()">
                        <span x-cloak x-show="soundEnabled">
                            <x-filament::icon class="orders-button-icon" icon="heroicon-o-speaker-x-mark" />
                        </span>

                        <span x-cloak x-show="! soundEnabled">
                            <x-filament::icon class="orders-button-icon" icon="heroicon-o-speaker-wave" />
                        </span>

                        <span
                            x-text="
                                soundEnabled
                                    ? 'Matikan Suara'
                                    : 'Aktifkan Suara'
                            "></span>
                    </button>
                </div>
            </div>

            {{-- Ilustrasi lonceng sebelah kanan --}}
            <div aria-hidden="true" class="orders-notification-illustration">
                <div class="orders-notification-ring ring-one"></div>

                <div class="orders-notification-ring ring-two"></div>

                <div class="orders-notification-ring ring-three"></div>

                <div class="orders-notification-icon-wrapper">
                    <x-filament::icon class="orders-notification-icon" icon="heroicon-o-bell-alert" />

                    <span class="orders-notification-dot">
                        !
                    </span>
                </div>
            </div>
        </section>

        {{-- ====================================================== --}}
        {{-- PEMERIKSAAN PESANAN BARU SETIAP 5 DETIK --}}
        {{-- ====================================================== --}}
        <div aria-hidden="true" class="hidden" wire:poll.5s="checkNewOrders"></div>

        {{-- ====================================================== --}}
        {{-- TAB DAN TABEL FILAMENT --}}
        {{-- ====================================================== --}}
        <div class="orders-content">
            {{ $this->content }}
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* ==========================================================
         * LAYOUT UTAMA
         * ========================================================== */

        .orders-page-shell {
            display: flex;
            width: 100%;
            min-width: 0;
            flex-direction: column;
            gap: 1.75rem;
        }

        .orders-content {
            width: 100%;
            min-width: 0;
        }

        /* ==========================================================
         * PANEL PEMANTAUAN PESANAN
         * ========================================================== */

        .orders-monitor-card {
            position: relative;
            display: grid;
            grid-template-columns:
                124px minmax(0, 1fr) 150px;
            width: 100%;
            min-height: 184px;
            align-items: center;
            gap: 1.5rem;
            overflow: hidden;
            padding: 1.75rem 2rem;
            border: 1px solid rgba(229, 231, 235, 0.95);
            border-radius: 1.25rem;
            background:
                radial-gradient(circle at 92% 50%,
                    rgba(251, 146, 60, 0.08),
                    transparent 25%),
                linear-gradient(135deg,
                    rgba(255, 255, 255, 1),
                    rgba(255, 251, 247, 0.96));
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                0 12px 32px rgba(15, 23, 42, 0.06);
        }

        .orders-monitor-card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            content: "";
            background: linear-gradient(90deg,
                    #f97316,
                    #fb923c,
                    rgba(251, 146, 60, 0.1));
        }

        /* ==========================================================
         * ILUSTRASI KIRI
         * ========================================================== */

        .orders-monitor-illustration {
            position: relative;
            display: flex;
            min-height: 120px;
            align-items: center;
            justify-content: center;
        }

        .orders-monitor-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
        }

        .orders-monitor-circle-large {
            width: 112px;
            height: 112px;
            border: 1px solid rgba(251, 146, 60, 0.32);
            background: rgba(255, 247, 237, 0.7);
        }

        .orders-monitor-circle-small {
            width: 76px;
            height: 76px;
            border: 1px solid rgba(251, 146, 60, 0.24);
            background: linear-gradient(145deg,
                    #fff7ed,
                    #ffedd5);
            box-shadow:
                0 10px 24px rgba(249, 115, 22, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .orders-monitor-main-icon {
            width: 2.5rem;
            height: 2.5rem;
            color: #ea580c;
        }

        .orders-monitor-decoration {
            position: absolute;
            display: block;
            border-radius: 9999px;
            background: #fdba74;
        }

        .decoration-one {
            top: 13px;
            right: 10px;
            width: 9px;
            height: 9px;
        }

        .decoration-two {
            bottom: 14px;
            left: 11px;
            width: 7px;
            height: 7px;
        }

        .decoration-three {
            right: 18px;
            bottom: 28px;
            width: 5px;
            height: 5px;
            opacity: 0.7;
        }

        /* ==========================================================
         * ISI PANEL
         * ========================================================== */

        .orders-monitor-content {
            position: relative;
            z-index: 2;
            min-width: 0;
        }

        .orders-monitor-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
        }

        .orders-monitor-heading h2 {
            margin: 0;
            color: #111827;
            font-size: 1.125rem;
            font-weight: 700;
            line-height: 1.5;
            letter-spacing: -0.015em;
        }

        .orders-status-badge {
            display: inline-flex;
            min-height: 28px;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }

        .orders-status-active {
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #15803d;
            background: rgba(240, 253, 244, 0.95);
        }

        .orders-status-icon {
            width: 0.95rem;
            height: 0.95rem;
        }

        .orders-monitor-description {
            max-width: 720px;
            margin: 0.7rem 0 0;
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.65;
        }

        .orders-monitor-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.15rem;
        }

        .orders-sound-status {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            border-radius: 0.75rem;
            font-size: 0.825rem;
            font-weight: 600;
            transition:
                color 180ms ease,
                background-color 180ms ease,
                border-color 180ms ease;
        }

        .orders-sound-status-active {
            border: 1px solid rgba(34, 197, 94, 0.22);
            color: #15803d;
            background: #f0fdf4;
        }

        .orders-sound-status-inactive {
            border: 1px solid rgba(156, 163, 175, 0.28);
            color: #6b7280;
            background: #f9fafb;
        }

        .orders-action-icon {
            width: 1rem;
            height: 1rem;
        }

        .orders-sound-button {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            border: 1px solid rgba(234, 88, 12, 0.14);
            border-radius: 0.75rem;
            color: #9a3412;
            background: linear-gradient(135deg,
                    #fed7aa,
                    #fdba74);
            box-shadow:
                0 1px 2px rgba(234, 88, 12, 0.08),
                0 6px 14px rgba(234, 88, 12, 0.12);
            font-size: 0.825rem;
            font-weight: 700;
            cursor: pointer;
            transition:
                transform 180ms ease,
                box-shadow 180ms ease,
                background 180ms ease;
        }

        .orders-sound-button:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg,
                    #fdba74,
                    #fb923c);
            box-shadow:
                0 2px 3px rgba(234, 88, 12, 0.1),
                0 10px 20px rgba(234, 88, 12, 0.16);
        }

        .orders-sound-button:active {
            transform: translateY(0);
        }

        .orders-sound-button:focus-visible {
            outline: 3px solid rgba(251, 146, 60, 0.25);
            outline-offset: 2px;
        }

        .orders-button-icon {
            width: 1.1rem;
            height: 1.1rem;
        }

        /* ==========================================================
         * ILUSTRASI LONCENG
         * ========================================================== */

        .orders-notification-illustration {
            position: relative;
            display: flex;
            width: 135px;
            height: 135px;
            align-items: center;
            justify-content: center;
            justify-self: end;
        }

        .orders-notification-ring {
            position: absolute;
            border: 1px solid rgba(251, 146, 60, 0.16);
            border-radius: 9999px;
        }

        .ring-one {
            width: 128px;
            height: 128px;
        }

        .ring-two {
            width: 94px;
            height: 94px;
        }

        .ring-three {
            width: 66px;
            height: 66px;
        }

        .orders-notification-icon-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            width: 64px;
            height: 64px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(251, 146, 60, 0.2);
            border-radius: 9999px;
            background: linear-gradient(145deg,
                    #fff7ed,
                    #ffedd5);
            box-shadow:
                0 12px 26px rgba(249, 115, 22, 0.14),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .orders-notification-icon {
            width: 2rem;
            height: 2rem;
            color: #f97316;
        }

        .orders-notification-dot {
            position: absolute;
            top: -3px;
            right: -3px;
            display: flex;
            width: 22px;
            height: 22px;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            border-radius: 9999px;
            color: #fff;
            background: #ef4444;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.28);
            font-size: 0.65rem;
            font-weight: 800;
        }

        /* ==========================================================
         * TAB FILAMENT
         * ========================================================== */

        .orders-content .fi-tabs {
            display: grid !important;
            grid-template-columns:
                repeat(5, minmax(0, 1fr)) !important;
            width: 100% !important;
            align-items: stretch !important;
            gap: 1rem !important;
            margin: 0 0 1.75rem !important;
            padding: 0 !important;
            overflow: visible !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .orders-content .fi-tabs>* {
            min-width: 0;
        }

        .orders-content .fi-tabs-item,
        .orders-content .fi-tabs [role="tab"] {
            display: flex !important;
            width: 100% !important;
            min-width: 0 !important;
            min-height: 64px !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.65rem !important;
            padding: 0.85rem 1rem !important;
            border: 1px solid rgba(229, 231, 235, 0.95) !important;
            border-radius: 1rem !important;
            color: #4b5563 !important;
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                0 6px 18px rgba(15, 23, 42, 0.045) !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            transition:
                transform 180ms ease,
                color 180ms ease,
                border-color 180ms ease,
                background-color 180ms ease,
                box-shadow 180ms ease !important;
        }

        .orders-content .fi-tabs-item:hover,
        .orders-content .fi-tabs [role="tab"]:hover {
            transform: translateY(-2px);
            border-color:
                rgba(251, 146, 60, 0.38) !important;
            color: #c2410c !important;
            background: #fffaf5 !important;
            box-shadow:
                0 2px 4px rgba(15, 23, 42, 0.05),
                0 10px 24px rgba(249, 115, 22, 0.09) !important;
        }

        .orders-content .fi-tabs-item[aria-selected="true"],
        .orders-content .fi-tabs [role="tab"][aria-selected="true"],
        .orders-content .fi-tabs-item.fi-active {
            border-color:
                rgba(249, 115, 22, 0.55) !important;
            color: #c2410c !important;
            background: linear-gradient(135deg,
                    rgba(255, 247, 237, 1),
                    rgba(255, 251, 247, 1)) !important;
            box-shadow:
                0 1px 2px rgba(249, 115, 22, 0.06),
                0 10px 24px rgba(249, 115, 22, 0.11) !important;
        }

        .orders-content .fi-tabs-item[aria-selected="true"] svg,
        .orders-content .fi-tabs [role="tab"][aria-selected="true"] svg,
        .orders-content .fi-tabs-item.fi-active svg {
            color: #f97316 !important;
        }

        .orders-content .fi-tabs-item-label {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .orders-content .fi-tabs .fi-badge {
            flex-shrink: 0;
            border-radius: 9999px !important;
        }

        .orders-content .fi-tabs-item::after,
        .orders-content .fi-tabs [role="tab"]::after {
            display: none !important;
        }

        /* ==========================================================
         * TABEL FILAMENT
         * ========================================================== */

        .orders-content .fi-ta {
            overflow: hidden;
            border: 1px solid rgba(229, 231, 235, 0.92);
            border-radius: 1.25rem !important;
            background: #fff;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                0 12px 32px rgba(15, 23, 42, 0.055) !important;
        }

        .orders-content .fi-ta-header {
            padding: 1rem 1.25rem !important;
            border-bottom: 1px solid rgba(229, 231, 235, 0.9);
            background: rgba(255, 255, 255, 0.98);
        }

        .orders-content .fi-ta-header-ctn {
            gap: 0.75rem !important;
        }

        .orders-content .fi-input-wrp {
            border-radius: 0.875rem !important;
            box-shadow:
                0 0 0 1px rgba(209, 213, 219, 0.9),
                0 2px 6px rgba(15, 23, 42, 0.03) !important;
        }

        .orders-content .fi-ta-table thead tr {
            background: linear-gradient(180deg,
                    #ffffff,
                    #fafafa);
        }

        .orders-content .fi-ta-table thead th {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
            color: #111827;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .orders-content .fi-ta-table tbody tr {
            transition:
                transform 160ms ease,
                background-color 160ms ease,
                box-shadow 160ms ease;
        }

        .orders-content .fi-ta-table tbody tr:hover>td {
            background-color: rgba(249, 250, 251, 0.75);
        }

        /* ==========================================================
         * WARNA BARIS BERDASARKAN STATUS
         * ========================================================== */

        tr.order-row-needs-attention>td {
            background-color: rgba(254, 242, 242, 0.78);
            border-top-color: rgba(239, 68, 68, 0.15);
            border-bottom-color: rgba(239, 68, 68, 0.15);
        }

        tr.order-row-needs-attention:hover>td {
            background-color:
                rgba(254, 226, 226, 0.72) !important;
        }

        tr.order-row-processing>td {
            background-color: rgba(239, 246, 255, 0.68);
        }

        tr.order-row-processing:hover>td {
            background-color:
                rgba(219, 234, 254, 0.62) !important;
        }

        tr.order-row-ready>td {
            background-color: rgba(240, 253, 244, 0.72);
        }

        tr.order-row-ready:hover>td {
            background-color:
                rgba(220, 252, 231, 0.68) !important;
        }

        /* ==========================================================
         * DARK MODE
         * ========================================================== */

        .dark .orders-monitor-card {
            border-color: rgba(255, 255, 255, 0.1);
            background:
                radial-gradient(circle at 92% 50%,
                    rgba(249, 115, 22, 0.12),
                    transparent 28%),
                linear-gradient(135deg,
                    rgba(17, 24, 39, 1),
                    rgba(23, 23, 23, 1));
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.2),
                0 14px 34px rgba(0, 0, 0, 0.22);
        }

        .dark .orders-monitor-heading h2 {
            color: #f9fafb;
        }

        .dark .orders-monitor-description {
            color: #9ca3af;
        }

        .dark .orders-monitor-circle-large {
            border-color: rgba(251, 146, 60, 0.24);
            background: rgba(124, 45, 18, 0.14);
        }

        .dark .orders-monitor-circle-small,
        .dark .orders-notification-icon-wrapper {
            border-color: rgba(251, 146, 60, 0.22);
            background: rgba(124, 45, 18, 0.25);
        }

        .dark .orders-notification-dot {
            border-color: #111827;
        }

        .dark .orders-sound-status-active {
            border-color: rgba(34, 197, 94, 0.2);
            color: #86efac;
            background: rgba(20, 83, 45, 0.25);
        }

        .dark .orders-sound-status-inactive {
            border-color: rgba(255, 255, 255, 0.1);
            color: #d1d5db;
            background: rgba(31, 41, 55, 0.75);
        }

        .dark .orders-content .fi-tabs-item,
        .dark .orders-content .fi-tabs [role="tab"] {
            border-color:
                rgba(255, 255, 255, 0.1) !important;
            color: #d1d5db !important;
            background:
                rgba(17, 24, 39, 0.96) !important;
        }

        .dark .orders-content .fi-tabs-item[aria-selected="true"],
        .dark .orders-content .fi-tabs [role="tab"][aria-selected="true"],
        .dark .orders-content .fi-tabs-item.fi-active {
            border-color:
                rgba(249, 115, 22, 0.5) !important;
            color: #fdba74 !important;
            background:
                rgba(124, 45, 18, 0.25) !important;
        }

        .dark .orders-content .fi-ta {
            border-color: rgba(255, 255, 255, 0.1);
            background: #111827;
        }

        .dark .orders-content .fi-ta-header {
            border-bottom-color: rgba(255, 255, 255, 0.08);
            background: rgba(17, 24, 39, 0.98);
        }

        .dark .orders-content .fi-ta-table thead tr {
            background: rgba(17, 24, 39, 0.98);
        }

        .dark .orders-content .fi-ta-table thead th {
            color: #f3f4f6;
        }

        .dark tr.order-row-needs-attention>td {
            background-color: rgba(127, 29, 29, 0.16);
        }

        .dark tr.order-row-processing>td {
            background-color: rgba(30, 64, 175, 0.13);
        }

        .dark tr.order-row-ready>td {
            background-color: rgba(20, 83, 45, 0.16);
        }

        /* ==========================================================
         * RESPONSIVE TABLET
         * ========================================================== */

        @media (max-width: 1100px) {
            .orders-monitor-card {
                grid-template-columns:
                    100px minmax(0, 1fr) 110px;
                padding: 1.5rem;
            }

            .orders-monitor-circle-large {
                width: 92px;
                height: 92px;
            }

            .orders-monitor-circle-small {
                width: 64px;
                height: 64px;
            }

            .orders-notification-illustration {
                width: 100px;
                height: 100px;
            }

            .ring-one {
                width: 100px;
                height: 100px;
            }

            .ring-two {
                width: 76px;
                height: 76px;
            }

            .ring-three {
                width: 56px;
                height: 56px;
            }

            .orders-content .fi-tabs {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr)) !important;
            }
        }

        /* ==========================================================
         * RESPONSIVE MOBILE
         * ========================================================== */

        @media (max-width: 767px) {
            .orders-page-shell {
                gap: 1.25rem;
            }

            .orders-monitor-card {
                display: flex;
                min-height: auto;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.35rem;
            }

            .orders-monitor-illustration {
                display: none;
            }

            .orders-notification-illustration {
                position: absolute;
                top: 0.75rem;
                right: 0.75rem;
                width: 70px;
                height: 70px;
                opacity: 0.35;
                pointer-events: none;
            }

            .ring-one {
                width: 70px;
                height: 70px;
            }

            .ring-two {
                width: 52px;
                height: 52px;
            }

            .ring-three {
                width: 38px;
                height: 38px;
            }

            .orders-notification-icon-wrapper {
                width: 40px;
                height: 40px;
            }

            .orders-notification-icon {
                width: 1.35rem;
                height: 1.35rem;
            }

            .orders-notification-dot {
                width: 18px;
                height: 18px;
                font-size: 0.55rem;
            }

            .orders-monitor-content {
                width: 100%;
            }

            .orders-monitor-heading {
                max-width: calc(100% - 60px);
                align-items: flex-start;
            }

            .orders-monitor-heading h2 {
                font-size: 1rem;
            }

            .orders-monitor-description {
                max-width: calc(100% - 30px);
                font-size: 0.825rem;
            }

            .orders-monitor-actions {
                width: 100%;
                align-items: stretch;
            }

            .orders-sound-status,
            .orders-sound-button {
                flex: 1 1 auto;
            }

            .orders-content .fi-tabs {
                display: flex !important;
                grid-template-columns: none !important;
                gap: 0.75rem !important;
                overflow-x: auto !important;
                padding: 0 0 0.65rem !important;
                scroll-behavior: smooth;
                scroll-snap-type: x mandatory;
                scrollbar-width: thin;
            }

            .orders-content .fi-tabs-item,
            .orders-content .fi-tabs [role="tab"] {
                width: auto !important;
                min-width: 190px !important;
                min-height: 58px !important;
                flex: 0 0 auto !important;
                scroll-snap-align: start;
            }
        }

        @media (max-width: 430px) {
            .orders-monitor-actions {
                flex-direction: column;
            }

            .orders-sound-status,
            .orders-sound-button {
                width: 100%;
            }
        }
    </style>
</x-filament-panels::page>
