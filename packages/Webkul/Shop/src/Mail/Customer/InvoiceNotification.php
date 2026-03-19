<?php

namespace Webkul\Shop\Mail\Customer;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Webkul\Shop\Mail\Mailable;
use Webkul\Customer\Contracts\CustomerTransaction;

class InvoiceNotification extends Mailable
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
            subject: trans('shop::app.emails.customers.invoice.subject', [
                'id' => $this->transaction->id,
                'date' => $this->transaction->created_at->format('d.m.Y')
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.customers.invoice',
        );
    }

    public function attachments(): array
    {
        $fileName = 'Счет_Оферта_' . $this->transaction->id . '_от_' . $this->transaction->created_at->format('d.m.Y') . '.pdf';

        return [
            Attachment::fromData(fn() => base64_decode($this->pdfData), $fileName)
                ->withMime('application/pdf'),
        ];
    }
}
