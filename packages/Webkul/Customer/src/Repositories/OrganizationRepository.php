<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;

class OrganizationRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return 'Webkul\Customer\Contracts\Organization';
    }
}
