<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CustomerTransaction as CustomerTransactionContract;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerProxy;

class CustomerTransaction extends Model implements CustomerTransactionContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'customer_id',
        'amount',
        'type',
        'status',
        'reference_type',
        'reference_id',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:4',
    ];

    /**
     * Get the customer that owns the transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the referenced model.
     */
    public function reference()
    {
        return $this->morphTo();
    }
}
