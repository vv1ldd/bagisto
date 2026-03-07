<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\OrganizationSettlementAccount as OrganizationSettlementAccountContract;

class OrganizationSettlementAccount extends Model implements OrganizationSettlementAccountContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization_settlement_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_id',
        'bic',
        'bank_name',
        'correspondent_account',
        'settlement_account',
        'is_default',
    ];

    /**
     * Get the organization that owns the settlement account.
     */
    public function organization()
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }
}
