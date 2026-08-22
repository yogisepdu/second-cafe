<?php

namespace App\Observers;

use App\Jobs\SendPaymentReceiptEmail;
use App\Models\Payment;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class PaymentObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Dipanggil ketika pembayaran baru dibuat.
     */
    public function created(Payment $payment): void
    {
        $this->dispatchReceiptEmail($payment);
    }

    /**
     * Dipanggil ketika pembayaran diperbarui.
     */
    public function updated(Payment $payment): void
    {
        if (! $payment->wasChanged('status')) {
            return;
        }

        $this->dispatchReceiptEmail($payment);
    }

    private function dispatchReceiptEmail(Payment $payment): void
    {
        /*
         * Email hanya dikirim jika pembayaran berhasil.
         */
        if ($payment->status !== Payment::STATUS_SUCCESS) {
            return;
        }

        /*
         * Jangan kirim lagi jika sudah pernah dikirim.
         */
        if (filled($payment->receipt_emailed_at)) {
            return;
        }

        SendPaymentReceiptEmail::dispatch(
            (int) $payment->getKey()
        );
    }
}
