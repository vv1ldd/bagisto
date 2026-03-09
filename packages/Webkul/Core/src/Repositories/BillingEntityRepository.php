<?php

namespace Webkul\Core\Repositories;

use Webkul\Core\Eloquent\Repository;

class BillingEntityRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return 'Webkul\Core\Contracts\BillingEntity';
    }

    /**
     * Set a billing entity as default.
     * Ensure only one entity holds is_default at a time.
     *
     * @param int $id
     * @return \Webkul\Core\Contracts\BillingEntity
     */
    public function setDefault($id)
    {
        $this->model->query()->update(['is_default' => 0]);

        $entity = $this->find($id);
        $entity->is_default = 1;
        $entity->save();

        return $entity;
    }
}
