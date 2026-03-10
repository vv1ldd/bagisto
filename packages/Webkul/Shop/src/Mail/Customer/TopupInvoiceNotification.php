<?php

namespace Webkul\Shop\Mail\Customer;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Webkul\Shop\Mail\Mailable;
use Webkul\Customer\Contracts\CustomerTransaction;

class TopupInvoiceNotification extends Mailable
{
    /**
     * Create a new mailable instance.
     *
     * @return void
     */
    public function __construct(
        public CustomerTransaction $transaction,
        public $pdfData
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [
                new Address($this->transaction->customer->email),
            ],
            subject: trans('shop::app.emails.customers.topup.subject', ['id' => $this->transaction->id]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.customers.topup-invoice',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => base64_decode($this->pdfData), 'proforma-invoice-' . $this->transaction->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
