@php
    $totalMenus = $categories->sum(fn($category) => $category->menus->count());
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <meta content="#f59e0b" name="theme-color">

    <title>
        Menu Second Cafe - {{ $cafeTable->table_number }}
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
            --success-soft: #dcfce7;
            --shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--background);
            color: var(--text);
            font-family: Inter, Arial, sans-serif;
        }

        button,
        input {
            font: inherit;
        }

        [hidden] {
            display: none !important;
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .topbar {
            position: sticky;
            z-index: 50;
            top: 0;
            border-bottom: 1px solid rgba(229, 231, 235, 0.9);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
        }

        .topbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 74px;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            min-width: 0;
            gap: 12px;
        }

        .brand-logo {
            display: grid;
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg,
                    var(--primary),
                    #fbbf24);
            color: var(--coffee);
            font-size: 17px;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.28);
        }

        .brand-name {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        .brand-subtitle {
            overflow: hidden;
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-badge {
            display: flex;
            align-items: center;
            flex: 0 0 auto;
            gap: 7px;
            padding: 9px 13px;
            border: 1px solid #fde68a;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 800;
        }

        /*
        |--------------------------------------------------------------------------
        | Hero dan pencarian
        |--------------------------------------------------------------------------
        */

        .hero {
            padding: 42px 0 24px;
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            padding: 34px;
            border-radius: 28px;
            background:
                radial-gradient(circle at top right,
                    rgba(251, 191, 36, 0.35),
                    transparent 36%),
                linear-gradient(135deg,
                    #292018,
                    #3f2c1f);
            color: #ffffff;
            box-shadow: var(--shadow);
        }

        .hero-card::after {
            position: absolute;
            right: -45px;
            bottom: -70px;
            width: 220px;
            height: 220px;
            border: 35px solid rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            content: "";
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 650px;
        }

        .hero-eyebrow {
            margin: 0 0 10px;
            color: #fcd34d;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(28px, 5vw, 44px);
            line-height: 1.12;
        }

        .hero-description {
            max-width: 560px;
            margin: 14px 0 0;
            color: #d1d5db;
            font-size: 15px;
            line-height: 1.7;
        }

        .search-wrapper {
            position: relative;
            z-index: 2;
            width: min(620px, calc(100% - 36px));
            margin: -26px auto 0;
        }

        .search-input {
            width: 100%;
            height: 56px;
            padding: 0 20px 0 50px;
            border: 1px solid var(--border);
            border-radius: 18px;
            outline: none;
            background: var(--surface);
            color: var(--text);
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.13);
            transition: 0.2s ease;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow:
                0 0 0 4px rgba(245, 158, 11, 0.15),
                0 14px 35px rgba(15, 23, 42, 0.10);
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 19px;
            color: var(--muted);
            transform: translateY(-50%);
            pointer-events: none;
        }

        /*
        |--------------------------------------------------------------------------
        | Filter kategori
        |--------------------------------------------------------------------------
        */

        .category-filter {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding: 28px 0 8px;
            scrollbar-width: none;
        }

        .category-filter::-webkit-scrollbar {
            display: none;
        }

        .category-button {
            flex: 0 0 auto;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--surface);
            color: #4b5563;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .category-button:hover,
        .category-button.active {
            border-color: var(--primary);
            background: var(--primary);
            color: var(--coffee);
        }

        /*
        |--------------------------------------------------------------------------
        | Daftar menu
        |--------------------------------------------------------------------------
        */

        .menu-content {
            padding: 20px 0 60px;
        }

        .category-section {
            margin-bottom: 42px;
        }

        .category-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .category-heading h2 {
            margin: 0;
            font-size: 24px;
        }

        .category-heading p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .menu-count {
            flex: 0 0 auto;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .menu-grid {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .menu-card {
            display: flex;
            min-width: 0;
            overflow: hidden;
            flex-direction: column;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: 0 5px 16px rgba(15, 23, 42, 0.05);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .menu-card:hover {
            border-color: #fcd34d;
            box-shadow: var(--shadow);
            transform: translateY(-4px);
        }

        .menu-media {
            position: relative;
            overflow: hidden;
            width: 100%;
            aspect-ratio: 16 / 9;
            background:
                linear-gradient(135deg, #fef3c7, #fde68a);
        }

        .menu-media img {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }

        .menu-card:hover .menu-media img {
            transform: scale(1.04);
        }

        .menu-placeholder {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: var(--primary-dark);
            font-size: 46px;
        }

        .category-label {
            position: absolute;
            z-index: 2;
            top: 12px;
            left: 12px;
            padding: 7px 10px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.91);
            color: var(--coffee);
            font-size: 11px;
            font-weight: 800;
            backdrop-filter: blur(8px);
        }

        .menu-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 18px;
        }

        .menu-name {
            margin: 0;
            font-size: 18px;
            line-height: 1.35;
        }

        .menu-description {
            display: -webkit-box;
            overflow: hidden;
            margin: 9px 0 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .menu-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            gap: 12px;
        }

        .menu-price {
            color: var(--primary-dark);
            font-size: 19px;
            font-weight: 900;
        }

        .availability {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 999px;
            background: var(--success-soft);
            color: var(--success);
            font-size: 11px;
            font-weight: 800;
        }

        .availability-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
        }

        /*
        |--------------------------------------------------------------------------
        | Kondisi kosong
        |--------------------------------------------------------------------------
        */

        .empty-state {
            padding: 50px 20px;
            border: 1px dashed #d1d5db;
            border-radius: 22px;
            background: var(--surface);
            text-align: center;
        }

        .empty-icon {
            margin-bottom: 12px;
            font-size: 44px;
        }

        .empty-state h2 {
            margin: 0;
            font-size: 20px;
        }

        .empty-state p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .footer {
            padding: 24px 0;
            border-top: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            text-align: center;
            font-size: 12px;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsif
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {
            .menu-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .container {
                width: min(100% - 24px, 1180px);
            }

            .topbar-content {
                min-height: 66px;
            }

            .brand-logo {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
                border-radius: 12px;
            }

            .brand-subtitle {
                max-width: 145px;
            }

            .table-badge {
                padding: 8px 11px;
                font-size: 12px;
            }

            .hero {
                padding-top: 20px;
            }

            .hero-card {
                padding: 26px 22px 48px;
                border-radius: 22px;
            }

            .search-wrapper {
                width: calc(100% - 28px);
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .category-section {
                margin-bottom: 34px;
            }

            .category-heading h2 {
                font-size: 21px;
            }
        }

        .add-menu-button {
            padding: 9px 15px;
            border: 0;
            border-radius: 999px;
            background: var(--primary);
            color: var(--coffee);
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
            transition: 0.2s ease;
        }

        .add-menu-button:hover {
            background: #fbbf24;
            transform: translateY(-1px);
        }

        .menu-dialog {
            width: min(520px, calc(100% - 24px));
            max-height: calc(100vh - 30px);
            overflow: hidden;
            padding: 0;
            border: 0;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.28);
        }

        .menu-dialog::backdrop {
            background: rgba(15, 23, 42, 0.68);
            backdrop-filter: blur(4px);
        }

        .dialog-content {
            max-height: calc(100vh - 30px);
            overflow-y: auto;
        }

        .dialog-image-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #fef3c7;
        }

        .dialog-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dialog-close {
            position: absolute;
            z-index: 2;
            top: 14px;
            right: 14px;
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            cursor: pointer;
            font-size: 20px;
        }

        .dialog-body {
            padding: 22px;
        }

        .dialog-title {
            margin: 0;
            font-size: 24px;
        }

        .dialog-description {
            margin: 9px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .dialog-price {
            display: block;
            margin-top: 12px;
            color: var(--primary-dark);
            font-size: 21px;
            font-weight: 900;
        }

        .level-information {
            margin-top: 20px;
            padding: 14px;
            border: 1px dashed #f59e0b;
            border-radius: 14px;
            background: #fffbeb;
        }

        .level-information strong {
            display: block;
            margin-bottom: 4px;
        }

        .level-information span {
            color: var(--muted);
            font-size: 12px;
        }

        .form-label {
            display: block;
            margin: 20px 0 8px;
            font-size: 13px;
            font-weight: 800;
        }

        .notes-input {
            width: 100%;
            min-height: 92px;
            padding: 12px;
            resize: vertical;
            border: 1px solid var(--border);
            border-radius: 14px;
            outline: none;
        }

        .notes-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.14);
        }

        .quantity-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 22px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quantity-button {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            font-size: 20px;
        }

        .quantity-value {
            min-width: 24px;
            text-align: center;
            font-weight: 900;
        }

        .dialog-submit {
            width: 100%;
            margin-top: 22px;
            padding: 14px;
            border: 0;
            border-radius: 14px;
            background: var(--primary);
            color: var(--coffee);
            cursor: pointer;
            font-weight: 900;
        }

        .dialog-submit:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .cart-bar {
            position: fixed;
            z-index: 40;
            right: 16px;
            bottom: 16px;
            left: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: min(600px, calc(100% - 32px));
            margin: auto;
            padding: 14px 16px;
            border-radius: 18px;
            background: var(--coffee);
            color: #ffffff;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.3);
        }

        .cart-summary {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .cart-count {
            color: #d1d5db;
            font-size: 12px;
        }

        .cart-total {
            font-size: 17px;
            font-weight: 900;
        }

        .cart-label {
            padding: 10px 14px;
            border-radius: 12px;
            background: var(--primary);
            color: var(--coffee);
            font-size: 13px;
            font-weight: 900;
        }

        .cart-label {
            text-decoration: none;
        }
    </style>

    <meta content="{{ csrf_token() }}" name="csrf-token">
</head>

<body>
    <header class="topbar">
        <div class="topbar-content container">
            <div class="brand">
                <div class="brand-logo">SC</div>

                <div>
                    <p class="brand-name">
                        Second Cafe
                    </p>

                    <p class="brand-subtitle">
                        {{ $cafeTable->name ?: 'Pemesanan menu digital' }}
                    </p>
                </div>
            </div>

            <div class="table-badge">
                <span>Meja</span>
                <strong>
                    {{ $cafeTable->table_number }}
                </strong>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-card">
                    <div class="hero-content">
                        <p class="hero-eyebrow">
                            Menu Digital
                        </p>

                        <h1>
                            Mau pesan apa hari ini?
                        </h1>

                        <p class="hero-description">
                            Pilih makanan dan minuman favorit
                            Anda. Pesanan akan dikirim langsung
                            dari meja
                            {{ $cafeTable->table_number }}.
                        </p>
                    </div>
                </div>

                <div class="search-wrapper">
                    <span class="search-icon">⌕</span>

                    <input aria-label="Cari menu" autocomplete="off" class="search-input" id="menuSearch"
                        placeholder="Cari makanan atau minuman..." type="search">
                </div>

                @if ($categories->isNotEmpty())
                    <nav aria-label="Filter kategori menu" class="category-filter">
                        <button class="category-button active" data-category="all" type="button">
                            Semua Menu
                        </button>

                        @foreach ($categories as $category)
                            <button class="category-button" data-category="{{ $category->id }}" type="button">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </nav>
                @endif
            </div>
        </section>

        <section class="menu-content">
            <div class="container">
                @forelse ($categories as $category)
                    <section class="category-section" data-section="{{ $category->id }}">
                        <div class="category-heading">
                            <div>
                                <h2>
                                    {{ $category->name }}
                                </h2>

                                @if ($category->description)
                                    <p>
                                        {{ $category->description }}
                                    </p>
                                @endif
                            </div>

                            <span class="menu-count">
                                {{ $category->menus->count() }}
                                menu
                            </span>
                        </div>

                        <div class="menu-grid">
                            @foreach ($category->menus as $menu)
                                <article class="menu-card" data-category="{{ $category->id }}"
                                    data-search="{{ $menu->name }}
                                        {{ $menu->description }}
                                        {{ $category->name }}">
                                    <div class="menu-media">
                                        <div class="menu-placeholder">
                                            🍽
                                        </div>

                                        @if ($menu->image)
                                            <img alt="{{ $menu->name }}" loading="lazy" onerror="this.remove()"
                                                src="{{ asset('storage/' . $menu->image) }}">
                                        @endif

                                        <span class="category-label">
                                            {{ $category->name }}
                                        </span>
                                    </div>

                                    <div class="menu-body">
                                        <h3 class="menu-name">
                                            {{ $menu->name }}
                                        </h3>

                                        <p class="menu-description">
                                            {{ $menu->description ?: 'Menu pilihan Second Cafe yang siap disajikan untuk Anda.' }}
                                        </p>

                                        <div class="menu-footer">
                                            <span class="menu-price">
                                                Rp{{ number_format($menu->price, 0, ',', '.') }}
                                            </span>

                                            <button class="add-menu-button"
                                                data-menu-description="{{ $menu->description }}"
                                                data-menu-id="{{ $menu->id }}"
                                                data-menu-image="{{ $menu->image ? asset('storage/' . $menu->image) : '' }}"
                                                data-menu-name="{{ $menu->name }}"
                                                data-menu-price="{{ $menu->price }}" type="button">
                                                + Tambah
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">🍽</div>

                        <h2>
                            Menu belum tersedia
                        </h2>

                        <p>
                            Silakan hubungi kasir untuk informasi
                            lebih lanjut.
                        </p>
                    </div>
                @endforelse

                <div class="empty-state" hidden id="noSearchResult">
                    <div class="empty-icon">🔍</div>

                    <h2>Menu tidak ditemukan</h2>

                    <p>
                        Coba gunakan nama menu atau kategori
                        yang berbeda.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            Second Cafe · {{ $totalMenus }} menu tersedia ·
            Meja {{ $cafeTable->table_number }}
        </div>
    </footer>

    <dialog class="menu-dialog" id="menuDialog">
        <div class="dialog-content">
            <div class="dialog-image-wrapper">
                <img alt="" class="dialog-image" id="dialogImage" src="">

                <button aria-label="Tutup" class="dialog-close" id="closeMenuDialog" type="button">
                    ×
                </button>
            </div>

            <form id="addToCartForm">
                <div class="dialog-body">
                    <input id="dialogMenuId" type="hidden">

                    <h2 class="dialog-title" id="dialogMenuName"></h2>

                    <p class="dialog-description" id="dialogMenuDescription"></p>

                    <span class="dialog-price" id="dialogMenuPrice"></span>

                    <div class="level-information">
                        <strong>Level Menu</strong>

                        <span>
                            Pilihan level akan tersedia pada
                            tahap pengembangan berikutnya.
                        </span>
                    </div>

                    <label class="form-label" for="dialogNotes">
                        Catatan Pesanan
                    </label>

                    <textarea class="notes-input" id="dialogNotes" maxlength="255"
                        placeholder="Contoh: Tidak pakai timun, sedikit gula, atau es dipisah"></textarea>

                    <div class="quantity-row">
                        <strong>Jumlah Pesanan</strong>

                        <div class="quantity-control">
                            <button class="quantity-button" id="decreaseQuantity" type="button">
                                −
                            </button>

                            <span class="quantity-value" id="dialogQuantity">
                                1
                            </span>

                            <button class="quantity-button" id="increaseQuantity" type="button">
                                +
                            </button>
                        </div>
                    </div>

                    <button class="dialog-submit" id="submitCartButton" type="submit">
                        Tambah Pesanan
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <div class="cart-bar" hidden id="cartBar">
        <div class="cart-summary">
            <span class="cart-count" id="cartCount"></span>

            <strong class="cart-total" id="cartTotal"></strong>
        </div>

        <a class="cart-label" href="{{ route('customer.cart.show', ['token' => $cafeTable->qr_token]) }}">
            Tinjau Pesanan
        </a>
    </div>

    <script>
        const searchInput =
            document.getElementById('menuSearch');

        const categoryButtons =
            document.querySelectorAll(
                '.category-button'
            );

        const menuCards =
            document.querySelectorAll('.menu-card');

        const categorySections =
            document.querySelectorAll(
                '.category-section'
            );

        const noSearchResult =
            document.getElementById(
                'noSearchResult'
            );

        let activeCategory = 'all';

        function applyMenuFilter() {
            const keyword = searchInput.value
                .trim()
                .toLowerCase();

            let visibleMenuCount = 0;

            menuCards.forEach((card) => {
                const menuCategory =
                    card.dataset.category;

                const menuSearch =
                    card.dataset.search.toLowerCase();

                const categoryMatches =
                    activeCategory === 'all' ||
                    menuCategory === activeCategory;

                const keywordMatches = !keyword ||
                    menuSearch.includes(keyword);

                const shouldShow =
                    categoryMatches &&
                    keywordMatches;

                card.hidden = !shouldShow;

                if (shouldShow) {
                    visibleMenuCount++;
                }
            });

            categorySections.forEach((section) => {
                const hasVisibleMenu =
                    section.querySelector(
                        '.menu-card:not([hidden])'
                    );

                section.hidden = !hasVisibleMenu;
            });

            noSearchResult.hidden =
                visibleMenuCount !== 0;
        }

        categoryButtons.forEach((button) => {
            button.addEventListener(
                'click',
                function() {
                    categoryButtons.forEach(
                        (item) => {
                            item.classList.remove(
                                'active'
                            );
                        }
                    );

                    this.classList.add('active');

                    activeCategory =
                        this.dataset.category;

                    applyMenuFilter();
                }
            );
        });

        searchInput.addEventListener(
            'input',
            applyMenuFilter
        );
    </script>

    <script>
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const cartStoreUrl = @json(route('customer.cart.store', [
                'token' => $cafeTable->qr_token,
            ]));

        const cartIndexUrl = @json(route('customer.cart.index', [
                'token' => $cafeTable->qr_token,
            ]));

        const menuDialog =
            document.getElementById('menuDialog');

        const addToCartForm =
            document.getElementById('addToCartForm');

        const dialogMenuId =
            document.getElementById('dialogMenuId');

        const dialogMenuName =
            document.getElementById('dialogMenuName');

        const dialogMenuDescription =
            document.getElementById(
                'dialogMenuDescription'
            );

        const dialogMenuPrice =
            document.getElementById('dialogMenuPrice');

        const dialogImage =
            document.getElementById('dialogImage');

        const dialogNotes =
            document.getElementById('dialogNotes');

        const dialogQuantity =
            document.getElementById('dialogQuantity');

        const submitCartButton =
            document.getElementById(
                'submitCartButton'
            );

        let currentQuantity = 1;
        let currentPrice = 0;

        function rupiah(value) {
            return new Intl.NumberFormat(
                'id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0,
                }
            ).format(value);
        }

        function updateDialogTotal() {
            dialogQuantity.textContent =
                currentQuantity;

            submitCartButton.textContent =
                'Tambah Pesanan · ' +
                rupiah(
                    currentPrice * currentQuantity
                );
        }

        function updateCartBar(cart) {
            const cartBar =
                document.getElementById('cartBar');

            if (cart.total_quantity < 1) {
                cartBar.hidden = true;
                return;
            }

            document.getElementById(
                    'cartCount'
                ).textContent =
                cart.total_quantity + ' item';

            document.getElementById(
                    'cartTotal'
                ).textContent =
                rupiah(cart.total_amount);

            cartBar.hidden = false;
        }

        document.querySelectorAll(
            '.add-menu-button'
        ).forEach((button) => {
            button.addEventListener(
                'click',
                function() {
                    currentQuantity = 1;

                    currentPrice = Number(
                        this.dataset.menuPrice
                    );

                    dialogMenuId.value =
                        this.dataset.menuId;

                    dialogMenuName.textContent =
                        this.dataset.menuName;

                    dialogMenuDescription.textContent =
                        this.dataset.menuDescription ||
                        'Menu pilihan Second Cafe.';

                    dialogMenuPrice.textContent =
                        rupiah(currentPrice);

                    dialogNotes.value = '';

                    if (this.dataset.menuImage) {
                        dialogImage.src =
                            this.dataset.menuImage;

                        dialogImage.alt =
                            this.dataset.menuName;

                        dialogImage.parentElement.hidden =
                            false;
                    } else {
                        dialogImage.removeAttribute('src');
                        dialogImage.parentElement.hidden =
                            true;
                    }

                    updateDialogTotal();
                    menuDialog.showModal();
                }
            );
        });

        document.getElementById(
            'closeMenuDialog'
        ).addEventListener('click', () => {
            menuDialog.close();
        });

        document.getElementById(
            'increaseQuantity'
        ).addEventListener('click', () => {
            if (currentQuantity < 99) {
                currentQuantity++;
                updateDialogTotal();
            }
        });

        document.getElementById(
            'decreaseQuantity'
        ).addEventListener('click', () => {
            if (currentQuantity > 1) {
                currentQuantity--;
                updateDialogTotal();
            }
        });

        addToCartForm.addEventListener(
            'submit',
            async (event) => {
                event.preventDefault();

                submitCartButton.disabled = true;
                submitCartButton.textContent =
                    'Menambahkan...';

                try {
                    const response = await fetch(
                        cartStoreUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                menu_id: Number(
                                    dialogMenuId.value
                                ),
                                quantity: currentQuantity,
                                notes: dialogNotes.value,
                            }),
                        }
                    );

                    const result =
                        await response.json();

                    if (!response.ok) {
                        throw new Error(
                            result.message ||
                            'Pesanan gagal ditambahkan.'
                        );
                    }

                    updateCartBar(result);
                    menuDialog.close();
                } catch (error) {
                    alert(error.message);
                    updateDialogTotal();
                } finally {
                    submitCartButton.disabled = false;
                }
            }
        );

        fetch(cartIndexUrl, {
                headers: {
                    'Accept': 'application/json',
                },
            })
            .then((response) => response.json())
            .then(updateCartBar);
    </script>

</body>

</html>
