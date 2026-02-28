<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CryptoTransaction as CryptoTransactionContract;

class CryptoTransaction extends Model implements CryptoTransactionContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_crypto_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'tx_id',
        'network',
        'from_address',
        'to_address',
        'amount',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:8',
    ];

    /**
     * Get the customer that owns the transaction.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass(), 'customer_id');
    }
}
