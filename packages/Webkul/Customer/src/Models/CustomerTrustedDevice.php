<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Models\Customer;

class CustomerTrustedDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'ip_address',
        'user_agent',
        'cookie_token',
        'last_used_at',
    ];

    /**
     * Get the customer that owns the trusted device.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
