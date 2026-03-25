<?php

namespace Webkul\Customer\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        \Webkul\Customer\Models\CompareItem::class,
        \Webkul\Customer\Models\Customer::class,
        \Webkul\Customer\Models\CustomerAddress::class,
        \Webkul\Customer\Models\CustomerGroup::class,
        \Webkul\Customer\Models\CustomerNote::class,
        \Webkul\Customer\Models\Wishlist::class,
        \Webkul\Customer\Models\CustomerLoginLog::class,
        \Webkul\Customer\Models\CustomerTransaction::class,
        \Webkul\Customer\Models\CryptoAddress::class,
        \Webkul\Customer\Models\CryptoTransaction::class,
        \Webkul\Customer\Models\CustomerBalance::class,
        \Webkul\Customer\Models\Organization::class,
        \Webkul\Customer\Models\OrganizationSettlementAccount::class,
        \Webkul\Customer\Models\Bank::class,
        \Webkul\Customer\Models\Handshake::class,
    ];
}
