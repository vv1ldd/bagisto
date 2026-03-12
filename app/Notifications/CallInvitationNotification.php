<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CallInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $callerName,
        public int $callerId
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('shop.customers.account.calls.index', ['caller_id' => $this->callerId]);

        Log::info('Generating CallInvitation email for: ' . $notifiable->email);

        return (new MailMessage)
            ->subject(trans('shop::app.emails.calls.invitation.subject', ['app_name' => config('app.name')]))
            ->view('shop::emails.customers.call-invitation', [
                'notifiable' => $notifiable,
                'callerName' => $this->callerName,
                'url'        => $url,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'caller_name' => $this->callerName,
            'caller_id'   => $this->callerId,
        ];
    }
}
