<?php

namespace Webkul\Shop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuestCallInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new mailable instance.
     *
     * @param  string  $callerName
     * @param  string  $callUrl
     * @return void
     */
    public function __construct(
        public string $callerName,
        public string $callUrl
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(core()->getSenderEmailDetails())
            ->subject('Приглашение в видеозвонок Meanly')
            ->view('shop::emails.guest-call-invitation');
    }
}
