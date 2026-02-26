<?php

namespace Webkul\Shop\Mail\Customer;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Webkul\Customer\Contracts\Customer;
use Webkul\Customer\Contracts\CustomerLoginLog;
use Webkul\Shop\Mail\Mailable;

class LoginNotification extends Mailable
{
    /**
     * Create a new mailable instance.
     *
     * @return void
     */
    public function __construct(
        public Customer $customer,
        public CustomerLoginLog $loginLog
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [
                new Address($this->customer->email, $this->customer->name),
            ],
            subject: trans('shop::app.emails.customers.login-notification.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.customers.login-notification',
        );
    }
}
