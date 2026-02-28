<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;

class CustomerTransactionRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Customer\Contracts\CustomerTransaction';
    }
}
