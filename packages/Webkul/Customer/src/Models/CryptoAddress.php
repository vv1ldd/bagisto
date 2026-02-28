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
        'verification_amount',
        'verified_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:18',
        'verification_amount' => 'decimal:18',
        'verified_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($cryptoAddress) {
            if (!$cryptoAddress->verification_amount) {
                // Generate a unique tiny amount for verification (e.g., 0.0001XXXX)
                // Using a random integer to avoid floating point issues during generation
                $randomPart = rand(1000, 9999);
                $cryptoAddress->verification_amount = (float) ("0.000" . rand(1, 9) . $randomPart);
            }
        });
    }

    /**
     * Check if the address is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Get the customer that owns the crypto address.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }
}
