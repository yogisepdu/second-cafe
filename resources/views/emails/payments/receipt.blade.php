<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Bukti Pembayaran {{ $payment->payment_code }}
    </title>
</head>

<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif; color:#1f2937;">
    <table
        role="presentation"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        style="background:#f3f4f6;"
    >
        <tr>
            <td align="center" style="padding:30px 12px;">
                <table
                    role="presentation"
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    style="max-width:680px; background:#ffffff; border:1px solid #e5e7eb; border-radius:14px;"
                >
                    <tr>
                        <td style="padding:28px 32px; background:#111827; color:#ffffff;">
                            <div style="font-size:13px; font-weight:bold; color:#fbbf24;">
                                SECOND CAFE
                            </div>

                            <h1 style="margin:8px 0 0; font-size:25px;">
                                Bukti Pembayaran
                            </h1>

                            <p style="margin:8px 0 0; color:#d1d5db;">
                                Pembayaran berhasil
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin-top:0;">
                                Halo
                                <strong>
                                    {{ $order->customer_name ?: 'Pelanggan' }}
                                </strong>,
                            </p>

                            <p style="color:#4b5563; line-height:1.7;">
                                Pembayaran pesanan Anda telah berhasil diterima.
                                Simpan email ini sebagai bukti pembayaran.
                            </p>

                            <table
                                width="100%"
                                cellpadding="7"
                                cellspacing="0"
                                style="margin:22px 0; background:#f9fafb; border:1px solid #e5e7eb;"
                            >
                                <tr>
                                    <td>Kode pembayaran</td>
                                    <td align="right">
                                        <strong>{{ $payment->payment_code }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Kode pesanan</td>
                                    <td align="right">
                                        <strong>{{ $order->order_code }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Kode kasir</td>
                                    <td align="right">
                                        <strong>{{ $order->cashier_code }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Meja</td>
                                    <td align="right">
                                        <strong>
                                            @if ($order->cafeTable?->table_number)
                                                Meja {{ $order->cafeTable->table_number }}
                                            @else
                                                Bawa Pulang
                                            @endif
                                        </strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Metode pembayaran</td>
                                    <td align="right">
                                        <strong>{{ $payment->method_label }}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Waktu pembayaran</td>
                                    <td align="right">
                                        <strong>
                                            {{ ($payment->paid_at ?? $payment->created_at)?->timezone(config('app.timezone'))->format('d M Y H:i') ?? '-' }}
                                            WIB
                                        </strong>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="font-size:17px;">
                                Detail Pesanan
                            </h2>

                            <table
                                width="100%"
                                cellpadding="10"
                                cellspacing="0"
                                style="border-collapse:collapse; border:1px solid #e5e7eb;"
                            >
                                <thead>
                                    <tr style="background:#f9fafb;">
                                        <th align="left" style="border-bottom:1px solid #e5e7eb;">
                                            Menu
                                        </th>

                                        <th align="center" style="border-bottom:1px solid #e5e7eb;">
                                            Jumlah
                                        </th>

                                        <th align="right" style="border-bottom:1px solid #e5e7eb;">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($order->items as $item)
                                        <tr>
                                            <td style="border-bottom:1px solid #e5e7eb;">
                                                <strong>{{ $item->menu_name }}</strong>

                                                @if (! empty($item->selected_options))
                                                    <div style="margin-top:4px; font-size:12px; color:#6b7280;">
                                                        @foreach ($item->selected_options as $option)
                                                            {{ $option['group'] ?? 'Pilihan' }}:
                                                            {{ $option['option'] ?? '-' }}

                                                            @if (! $loop->last)
                                                                •
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if (filled($item->notes))
                                                    <div style="margin-top:4px; font-size:12px; color:#6b7280;">
                                                        Catatan: {{ $item->notes }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td
                                                align="center"
                                                style="border-bottom:1px solid #e5e7eb;"
                                            >
                                                {{ $item->quantity }}
                                            </td>

                                            <td
                                                align="right"
                                                style="border-bottom:1px solid #e5e7eb;"
                                            >
                                                Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" align="center">
                                                Detail menu tidak tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <table
                                width="100%"
                                cellpadding="6"
                                cellspacing="0"
                                style="margin-top:18px;"
                            >
                                <tr>
                                    <td>Subtotal</td>
                                    <td align="right">
                                        Rp{{ number_format((float) $order->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="font-size:16px; font-weight:bold;">
                                        Total Dibayar
                                    </td>

                                    <td
                                        align="right"
                                        style="font-size:20px; font-weight:bold; color:#15803d;"
                                    >
                                        Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>

                                @if ($payment->method === \App\Models\Payment::METHOD_CASHIER)
                                    <tr>
                                        <td>Uang diterima</td>
                                        <td align="right">
                                            Rp{{ number_format((float) $payment->amount_received, 0, ',', '.') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Kembalian</td>
                                        <td align="right">
                                            Rp{{ number_format((float) $payment->change_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            @if (filled($order->notes))
                                <div style="margin-top:20px; padding:14px; background:#fffbeb; color:#92400e;">
                                    <strong>Catatan pesanan:</strong>
                                    {{ $order->notes }}
                                </div>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td
                            align="center"
                            style="padding:20px; background:#f9fafb; border-top:1px solid #e5e7eb; color:#6b7280; font-size:12px;"
                        >
                            Email ini dikirim otomatis setelah pembayaran
                            berhasil tercatat di sistem Second Cafe.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>