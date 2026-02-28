<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CryptoAddress as CryptoAddressContract;

class CryptoAddress extends Model implements CryptoAddressContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_crypto_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'network',
        'address',
        'balance',
        'last_sync_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:18',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the customer that owns the crypto address.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }
}
