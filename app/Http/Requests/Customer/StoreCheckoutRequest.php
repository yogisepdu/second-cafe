<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Customer diperbolehkan melakukan checkout.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Membersihkan data sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $customerPhone = preg_replace(
            '/\s+/',
            '',
            trim((string) $this->input('customer_phone'))
        ) ?? '';

        $this->merge([
            'customer_name' => trim(
                (string) $this->input('customer_name')
            ),

            'customer_phone' => $customerPhone,

            'customer_email' => strtolower(
                trim(
                    (string) $this->input('customer_email')
                )
            ),

            'notes' => filled($this->input('notes'))
                ? trim((string) $this->input('notes'))
                : null,
        ]);
    }

    /**
     * Aturan validasi checkout.
     */
    public function rules(): array
    {
        return [
            'checkout_token' => [
                'required',
                'uuid',
            ],

            'customer_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],

            'customer_phone' => [
                'required',
                'string',
                'min:9',
                'max:20',
                'regex:/^[0-9+\-()]+$/',
            ],

            'customer_email' => [
                'required',
                'string',
                'email:rfc',
                'max:150',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cashier',
                    'online',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],

            'confirmation' => [
                'required',
                'accepted',
            ],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'checkout_token.required' =>
            'Sesi checkout tidak ditemukan.',

            'checkout_token.uuid' =>
            'Sesi checkout tidak valid.',

            'customer_name.required' =>
            'Nama pelanggan wajib diisi.',

            'customer_name.string' =>
            'Nama pelanggan harus berupa teks.',

            'customer_name.min' =>
            'Nama pelanggan minimal 3 karakter.',

            'customer_name.max' =>
            'Nama pelanggan maksimal 100 karakter.',

            'customer_phone.required' =>
            'Nomor HP wajib diisi.',

            'customer_phone.string' =>
            'Nomor HP harus berupa teks.',

            'customer_phone.min' =>
            'Nomor HP minimal 9 karakter.',

            'customer_phone.max' =>
            'Nomor HP maksimal 20 karakter.',

            'customer_phone.regex' =>
            'Format nomor HP tidak valid.',

            'customer_email.required' =>
            'Email untuk pengiriman bukti pembayaran wajib diisi.',

            'customer_email.string' =>
            'Email harus berupa teks.',

            'customer_email.email' =>
            'Format alamat email tidak valid.',

            'customer_email.max' =>
            'Email maksimal 150 karakter.',

            'payment_method.required' =>
            'Silakan pilih metode pembayaran.',

            'payment_method.in' =>
            'Metode pembayaran yang dipilih tidak tersedia.',

            'notes.string' =>
            'Catatan pesanan harus berupa teks.',

            'notes.max' =>
            'Catatan pesanan maksimal 500 karakter.',

            'confirmation.required' =>
            'Konfirmasi pesanan wajib disetujui.',

            'confirmation.accepted' =>
            'Anda harus menyetujui konfirmasi pesanan.',
        ];
    }

    /**
     * Nama atribut untuk pesan validasi.
     */
    public function attributes(): array
    {
        return [
            'checkout_token' => 'sesi checkout',
            'customer_name' => 'nama pelanggan',
            'customer_phone' => 'nomor HP',
            'customer_email' => 'email bukti pembayaran',
            'payment_method' => 'metode pembayaran',
            'notes' => 'catatan pesanan',
            'confirmation' => 'konfirmasi pesanan',
        ];
    }
}
