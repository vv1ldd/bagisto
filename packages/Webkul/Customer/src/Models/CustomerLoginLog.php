<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CustomerLoginLog as CustomerLoginLogContract;
use Webkul\Customer\Models\CustomerProxy;

class CustomerLoginLog extends Model implements CustomerLoginLogContract
{
    protected $table = 'customer_login_logs';

    protected $fillable = [
        'customer_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_name',
        'platform',
        'browser',
        'last_active_at',
        'logged_out_at',
    ];

    /**
     * Get the customer record associated with the log.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }
}
