<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;

class BankRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Webkul\Customer\Contracts\Bank';
    }
}
