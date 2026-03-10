<?php

namespace Webkul\Core\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
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

    /**
     * Get the default billing entity.
     *
     * @return \Webkul\Core\Contracts\BillingEntity|null
     */
    public function getDefault()
    {
        return $this->model->query()->where('is_default', 1)->first() ?: $this->model->query()->first();
    }

    /**
     * Upload seal image.
     *
     * @param array $data
     * @param \Webkul\Core\Contracts\BillingEntity $billingEntity
     * @return void
     */
    public function uploadSeal($data, $billingEntity)
    {
        if (isset($data['seal'])) {
            foreach ($data['seal'] as $imageId => $image) {
                $file = 'seal.' . $imageId;

                if (request()->hasFile($file)) {
                    if ($billingEntity->seal) {
                        Storage::delete($billingEntity->seal);
                    }

                    $uploadedFile = request()->file($file);

                    if ($uploadedFile->getClientOriginalExtension() == 'svg') {
                        $billingEntity->seal = $uploadedFile->store('billing_entities/' . $billingEntity->id);
                    } else {
                        $manager = new ImageManager;

                        $image = $manager->make($uploadedFile)->encode('webp');

                        $billingEntity->seal = 'billing_entities/' . $billingEntity->id . '/' . Str::random(40) . '.webp';

                        Storage::put($billingEntity->seal, $image);
                    }

                    $billingEntity->save();
                }
            }
        } else {
            if ($billingEntity->seal) {
                Storage::delete($billingEntity->seal);
            }

            $billingEntity->seal = null;

            $billingEntity->save();
        }
    }
}
