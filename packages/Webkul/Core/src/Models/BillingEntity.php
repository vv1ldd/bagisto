<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Core\Contracts\BillingEntity as BillingEntityContract;
use Webkul\Category\Models\CategoryProxy;

class BillingEntity extends Model implements BillingEntityContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'billing_entities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'inn',
        'kpp',
        'address',
        'bank_name',
        'bic',
        'settlement_account',
        'correspondent_account',
        'director_name',
        'accountant_name',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the categories assigned to this billing entity.
     */
    public function categories()
    {
        return $this->hasMany(CategoryProxy::modelClass());
    }
}
