<?php

namespace Webkul\Shop\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallSignal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $toUserId
     * @param  int  $fromUserId
     * @param  array  $signalData
     * @return void
     */
    public function __construct(
        public int $toUserId,
        public int $fromUserId,
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
        return new PrivateChannel('user.' . $this->toUserId);
    }

    /**
     * Broadcast with data.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'from_user_id' => $this->fromUserId,
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
