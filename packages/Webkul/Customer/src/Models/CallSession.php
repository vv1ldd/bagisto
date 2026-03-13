<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CallSession as CallSessionContract;

class CallSession extends Model implements CallSessionContract
{
    protected $fillable = [
        'uuid',
        'caller_name',
        'caller_email',
        'recipient_email',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];
}
