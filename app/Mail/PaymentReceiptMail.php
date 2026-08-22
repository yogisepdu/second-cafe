<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Payment $payment
    ) {
        $this->payment->loadMissing([
            'order.cafeTable',
            'order.items',
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Pembayaran '
                . $this->payment->payment_code
                . ' - Second Cafe'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payments.receipt',
            text: 'emails.payments.receipt-text',
            with: [
                'payment' => $this->payment,
                'order' => $this->payment->order,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
