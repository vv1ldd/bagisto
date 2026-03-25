<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\Handshake as HandshakeContract;
use Webkul\Customer\Models\CustomerProxy;

class Handshake extends Model implements HandshakeContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'handshakes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
    ];

    /**
     * Get the sender customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(CustomerProxy::modelClass(), 'sender_id');
    }

    /**
     * Get the receiver customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(CustomerProxy::modelClass(), 'receiver_id');
    }

    /**
     * Check if the handshake is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the handshake is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
