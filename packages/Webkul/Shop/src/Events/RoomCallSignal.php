<?php

namespace Webkul\Shop\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomCallSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $uuid
     * @param  string  $senderName
     * @param  array  $signalData
     * @return void
     */
    public function __construct(
        public string $uuid,
        public string $senderName,
        public array $signalData
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('call.' . $this->uuid);
    }

    /**
     * Broadcast with data.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'sender_name' => $this->senderName,
            'signal_data' => $this->signalData,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'call-signal';
    }
}
