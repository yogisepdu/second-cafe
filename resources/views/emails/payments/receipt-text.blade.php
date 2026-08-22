SECOND CAFE
BUKTI PEMBAYARAN

Halo {{ $order->customer_name ?: 'Pelanggan' }},

Pembayaran pesanan Anda telah berhasil diterima.

Kode pembayaran: {{ $payment->payment_code }}
Kode pesanan: {{ $order->order_code }}
Kode kasir: {{ $order->cashier_code }}
Metode pembayaran: {{ $payment->method_label }}
Waktu pembayaran: {{ ($payment->paid_at ?? $payment->created_at)?->timezone(config('app.timezone'))->format('d M Y H:i') ?? '-' }} WIB

DETAIL PESANAN

@forelse ($order->items as $item)
{{ $item->quantity }}x {{ $item->menu_name }}
Subtotal: Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}

@if (! empty($item->selected_options))
Pilihan:
@foreach ($item->selected_options as $option)
- {{ $option['group'] ?? 'Pilihan' }}: {{ $option['option'] ?? '-' }}
@endforeach
@endif

@if (filled($item->notes))
Catatan: {{ $item->notes }}
@endif

@empty
Detail menu tidak tersedia.
@endforelse

Subtotal pesanan:
Rp{{ number_format((float) $order->subtotal, 0, ',', '.') }}

Total dibayar:
Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}

@if ($payment->method === \App\Models\Payment::METHOD_CASHIER)
Uang diterima:
Rp{{ number_format((float) $payment->amount_received, 0, ',', '.') }}

Kembalian:
Rp{{ number_format((float) $payment->change_amount, 0, ',', '.') }}
@endif

@if (filled($order->notes))
Catatan pesanan:
{{ $order->notes }}
@endif

Simpan email ini sebagai bukti pembayaran resmi dari Second Cafe.