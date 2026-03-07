<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\Organization as OrganizationContract;
use Webkul\Customer\Models\OrganizationSettlementAccountProxy;

class Organization extends Model implements OrganizationContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'alias',
        'name',
        'inn',
        'kpp',
        'address',
        'bank_name',
        'bic',
        'settlement_account',
        'correspondent_account',
    ];

    /**
     * Get the customer that owns the organization.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }

    /**
     * Get the settlement accounts for the organization.
     */
    public function settlementAccounts()
    {
        return $this->hasMany(OrganizationSettlementAccountProxy::modelClass());
    }
}
