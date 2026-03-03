<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Customer\Repositories\OrganizationRepository;
use Webkul\Shop\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;

class OrganizationController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository)
    {
    }

    /**
     * Customer organizations.
     */
    public function index(): JsonResource
    {
        $customer = auth()->guard('customer')->user();

        return OrganizationResource::collection($customer->organizations);
    }

    /**
     * Create a new organization for customer.
     */
    public function store(Request $request): JsonResource
    {
        $customer = auth()->guard('customer')->user();

        $request->validate([
            'name' => 'required',
            'inn' => 'required',
        ]);

        $organization = $this->organizationRepository->create(array_merge($request->only([
            'name',
            'inn',
            'kpp',
            'address',
        ]), [
            'customer_id' => $customer->id,
        ]));

        return new JsonResource([
            'data' => new OrganizationResource($organization),
            'message' => 'Организация успешно добавлена.',
        ]);
    }
}
