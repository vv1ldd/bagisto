<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $url = route('shop.customers.account.calls', ['caller_id' => $this->callerId]);

        \Log::info('Generating CallInvitation email for: ' . $notifiable->email);

        return (new MailMessage)
            ->subject('Incoming Call Invitation - ' . config('app.name'))
            ->greeting('Hello, ' . $notifiable->first_name . '!')
            ->line('**' . $this->callerName . '** is inviting you to a secure P2P video call on Meanly.')
            ->line('Click the button below to join the call.')
            ->action('Join Call', $url)
            ->line('If you are not logged in, you will be redirected to the login page first.')
            ->line('Thank you for using Meanly!');
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
