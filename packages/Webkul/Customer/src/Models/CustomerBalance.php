<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CustomerBalance as CustomerBalanceContract;

class CustomerBalance extends Model implements CustomerBalanceContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_balances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'currency_code',
        'amount',
    ];

    /**
     * Get the customer that owns the balance.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }
}
