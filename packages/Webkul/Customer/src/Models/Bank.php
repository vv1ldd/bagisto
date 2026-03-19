<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\Bank as BankContract;

class Bank extends Model implements BankContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bic',
        'name',
        'correspondent_account',
        'address',
        'swift',
    ];
}
