<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Laporan Penjualan -
        {{ $report['period_label'] }}
    </title>

    <style>
        @page {
            margin: 25px 28px 38px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            line-height: 1.45;
        }

        .header {
            width: 100%;
            margin-bottom: 14px;
            padding: 16px 18px;
            color: #ffffff;
            border-radius: 9px;
            background: #0f172a;
        }

        .header-table,
        .summary-table,
        .analysis-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-mark {
            display: inline-block;
            width: 38px;
            height: 38px;
            margin-right: 10px;
            color: #ffffff;
            border-radius: 8px;
            background: #0f766e;
            font-size: 18px;
            font-weight: bold;
            line-height: 38px;
            text-align: center;
            vertical-align: middle;
        }

        .brand-name {
            color: #99f6e4;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .report-title {
            color: #ffffff;
            font-size: 19px;
            font-weight: bold;
        }

        .header-meta {
            color: #cbd5e1;
            font-size: 8px;
            text-align: right;
        }

        .period-pill {
            display: inline-block;
            margin-top: 6px;
            padding: 5px 9px;
            color: #ccfbf1;
            border: 1px solid #334155;
            border-radius: 12px;
            background: #1e293b;
            font-weight: bold;
        }

        .section-title {
            margin: 14px 0 7px;
            color: #0f172a;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .section-indicator {
            display: inline-block;
            width: 4px;
            height: 11px;
            margin-right: 6px;
            border-radius: 2px;
            background: #0f766e;
            vertical-align: -2px;
        }

        .summary-table {
            table-layout: fixed;
        }

        .summary-table td {
            width: 20%;
            padding-right: 7px;
            vertical-align: top;
        }

        .summary-table td:last-child {
            padding-right: 0;
        }

        .summary-card {
            min-height: 58px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .summary-card.success {
            border-color: #99f6e4;
            background: #f0fdfa;
        }

        .summary-card.warning {
            border-color: #fde68a;
            background: #fffbeb;
        }

        .summary-label {
            margin-bottom: 4px;
            color: #64748b;
            font-size: 6.7px;
            font-weight: bold;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .summary-value {
            color: #0f172a;
            font-size: 13px;
            font-weight: bold;
        }

        .summary-note {
            margin-top: 3px;
            color: #64748b;
            font-size: 6.4px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            padding: 7px 5px;
            color: #ffffff;
            border: 1px solid #334155;
            background: #1e293b;
            font-size: 6.2px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 6px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            color: #0f172a;
            font-weight: bold;
        }

        .muted {
            color: #64748b;
            font-size: 6.5px;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 8px;
            font-size: 6px;
            font-weight: bold;
            white-space: nowrap;
        }

        .badge-success {
            color: #166534;
            background: #dcfce7;
        }

        .badge-warning {
            color: #92400e;
            background: #fef3c7;
        }

        .badge-danger {
            color: #991b1b;
            background: #fee2e2;
        }

        .badge-info {
            color: #155e75;
            background: #cffafe;
        }

        .badge-gray {
            color: #475569;
            background: #e2e8f0;
        }

        .analysis-table td {
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }

        .analysis-table td:last-child {
            padding-right: 0;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table th {
            padding: 6px;
            color: #ffffff;
            background: #0f766e;
            font-size: 6.5px;
            text-align: left;
            text-transform: uppercase;
        }

        .mini-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .empty-state {
            padding: 20px;
            color: #64748b;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -25px;
            left: 0;
            padding-top: 6px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            font-size: 6.5px;
        }

        .footer-page {
            float: right;
        }

        .footer-page::after {
            content: "Halaman " counter(page);
        }

        .page-break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $summary = $report['summary'];

        $logoPath = public_path('images/logo.png');
    @endphp

    <div class="footer">
        Dokumen dibuat otomatis oleh
        {{ $report['app_name'] }}

        &middot;

        {{ $report['generated_at']->format('d/m/Y H:i') }}

        <span class="footer-page"></span>
    </div>

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 68%;">
                    @if (file_exists($logoPath))
                        <img alt="Logo" src="{{ $logoPath }}"
                            style="
                            width: 38px;
                            height: 38px;
                            margin-right: 10px;
                            vertical-align: middle;
                        ">
                    @else
                        <span class="brand-mark">
                            {{ mb_strtoupper(mb_substr($report['app_name'], 0, 1)) }}
                        </span>
                    @endif

                    <span
                        style="
                        display: inline-block;
                        vertical-align: middle;
                    ">
                        <span class="brand-name">
                            {{ $report['app_name'] }}
                        </span>

                        <br>

                        <span class="report-title">
                            Laporan Penjualan
                        </span>
                    </span>
                </td>

                <td class="header-meta" style="width: 32%;">
                    Disiapkan oleh

                    <strong style="color: #ffffff;">
                        {{ $report['generated_by'] }}
                    </strong>

                    <br>

                    {{ $report['generated_at']->locale('id')->translatedFormat('d F Y, H:i') }}

                    <br>

                    <span class="period-pill">
                        {{ $report['period_label'] }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">
        <span class="section-indicator"></span>
        Ringkasan Kinerja
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-card">
                    <div class="summary-label">
                        Total Pesanan
                    </div>

                    <div class="summary-value">
                        {{ number_format($summary['total_orders'], 0, ',', '.') }}
                    </div>

                    <div class="summary-note">
                        Pesanan pada periode
                    </div>
                </div>
            </td>

            <td>
                <div class="summary-card success">
                    <div class="summary-label">
                        Pendapatan Berhasil
                    </div>

                    <div class="summary-value">
                        Rp
                        {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                    </div>

                    <div class="summary-note">
                        Pembayaran berhasil
                    </div>
                </div>
            </td>

            <td>
                <div class="summary-card success">
                    <div class="summary-label">
                        Transaksi Berhasil
                    </div>

                    <div class="summary-value">
                        {{ number_format($summary['successful_transactions'], 0, ',', '.') }}
                    </div>

                    <div class="summary-note">
                        Pembayaran terverifikasi
                    </div>
                </div>
            </td>

            <td>
                <div class="summary-card">
                    <div class="summary-label">
                        Item Terjual
                    </div>

                    <div class="summary-value">
                        {{ number_format($summary['total_items'], 0, ',', '.') }}
                    </div>

                    <div class="summary-note">
                        Dari pesanan dibayar
                    </div>
                </div>
            </td>

            <td>
                <div class="summary-card warning">
                    <div class="summary-label">
                        Menunggu Pembayaran
                    </div>

                    <div class="summary-value">
                        {{ number_format($summary['waiting_payments'], 0, ',', '.') }}
                    </div>

                    <div class="summary-note">
                        Unpaid atau pending
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">
        <span class="section-indicator"></span>
        Detail Transaksi
    </div>

    @if ($report['rows']->isEmpty())
        <div class="empty-state">
            Tidak ada pesanan pada periode
            {{ $report['period_label'] }}.
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 3%;">
                        No.
                    </th>

                    <th style="width: 7%;">
                        Waktu
                    </th>

                    <th style="width: 9%;">
                        Kode
                    </th>

                    <th style="width: 9%;">
                        Pelanggan / Meja
                    </th>

                    <th style="width: 17%;">
                        Item Pesanan
                    </th>

                    <th class="text-center" style="width: 4%;">
                        Qty
                    </th>

                    <th class="text-right" style="width: 8%;">
                        Tagihan
                    </th>

                    <th style="width: 8%;">
                        Metode
                    </th>

                    <th style="width: 9%;">
                        Pembayaran
                    </th>

                    <th style="width: 8%;">
                        Status Pesanan
                    </th>

                    <th style="width: 10%;">
                        Kode / Kasir
                    </th>

                    <th style="width: 8%;">
                        Dibayar
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach ($report['rows'] as $row)
                    @php
                        $paymentBadge = match ($row['payment_status_value']) {
                            \App\Models\Order::PAYMENT_STATUS_PAID => 'badge-success',

                            \App\Models\Order::PAYMENT_STATUS_PENDING => 'badge-warning',

                            \App\Models\Order::PAYMENT_STATUS_FAILED,
                            \App\Models\Order::PAYMENT_STATUS_CANCELLED
                                => 'badge-danger',

                            default => 'badge-gray',
                        };

                        $orderBadge = match ($row['order_status_value']) {
                            \App\Models\Order::STATUS_COMPLETED => 'badge-success',

                            \App\Models\Order::STATUS_CANCELLED => 'badge-danger',

                            \App\Models\Order::STATUS_READY => 'badge-info',

                            \App\Models\Order::STATUS_WAITING_PAYMENT,
                            \App\Models\Order::STATUS_WAITING_VERIFICATION
                                => 'badge-warning',

                            default => 'badge-info',
                        };
                    @endphp

                    <tr>
                        <td class="text-center">
                            {{ $row['number'] }}
                        </td>

                        <td>
                            {{ $row['ordered_at'] }}
                        </td>

                        <td class="font-bold">
                            {{ $row['order_code'] }}
                        </td>

                        <td>
                            <span class="font-bold">
                                {{ $row['customer_name'] }}
                            </span>

                            <br>

                            <span class="muted">
                                {{ $row['table'] }}
                            </span>
                        </td>

                        <td>
                            {{ $row['items'] }}

                            @if ($row['categories'] !== '-')
                                <br>

                                <span class="muted">
                                    {{ $row['categories'] }}
                                </span>
                            @endif
                        </td>

                        <td class="text-center font-bold">
                            {{ $row['total_quantity'] }}
                        </td>

                        <td class="text-right font-bold">
                            Rp
                            {{ number_format($row['total_amount'], 0, ',', '.') }}
                        </td>

                        <td>
                            {{ $row['order_payment_method'] }}

                            <br>

                            <span class="muted">
                                {{ $row['payment_method'] }}
                            </span>
                        </td>

                        <td>
                            <span class="badge {{ $paymentBadge }}">
                                {{ $row['payment_status'] }}
                            </span>
                        </td>

                        <td>
                            <span class="badge {{ $orderBadge }}">
                                {{ $row['order_status'] }}
                            </span>
                        </td>

                        <td>
                            <span class="font-bold">
                                {{ $row['payment_code'] }}
                            </span>

                            <br>

                            <span class="muted">
                                {{ $row['verified_by'] }}
                            </span>
                        </td>

                        <td>
                            {{ $row['paid_at'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="page-break-avoid">
        <div class="section-title">
            <span class="section-indicator"></span>
            Analisis Produk
        </div>

        <table class="analysis-table">
            <tr>
                <td>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">
                                    #
                                </th>

                                <th>
                                    Menu Terlaris
                                </th>

                                <th class="text-center" style="width: 18%;">
                                    Terjual
                                </th>

                                <th class="text-right" style="width: 28%;">
                                    Nilai
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($report['menu_best_sellers']
                        as $index => $menu)
                                <tr>
                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="font-bold">
                                        {{ $menu['name'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ number_format($menu['quantity'], 0, ',', '.') }}
                                    </td>

                                    <td class="text-right">
                                        Rp
                                        {{ number_format($menu['revenue'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="muted" colspan="4">
                                        Belum ada penjualan berhasil.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                <td>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">
                                    #
                                </th>

                                <th>
                                    Kategori Terlaris
                                </th>

                                <th class="text-center" style="width: 18%;">
                                    Terjual
                                </th>

                                <th class="text-right" style="width: 28%;">
                                    Nilai
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($report['category_best_sellers']
                        as $index => $category)
                                <tr>
                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="font-bold">
                                        {{ $category['name'] }}
                                    </td>

                                    <td class="text-center">
                                        {{ number_format($category['quantity'], 0, ',', '.') }}
                                    </td>

                                    <td class="text-right">
                                        Rp
                                        {{ number_format($category['revenue'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="muted" colspan="4">
                                        Belum ada kategori terjual.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
