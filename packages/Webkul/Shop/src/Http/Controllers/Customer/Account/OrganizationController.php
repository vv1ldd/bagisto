<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Support\Facades\Event;
use Webkul\Customer\Repositories\OrganizationRepository;
use Webkul\Shop\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationController extends Controller
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
     * Organization route index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('shop::customers.account.organizations.index')->with('organizations', auth()->guard('customer')->user()->organizations);
    }

    /**
     * Show the organization create form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('shop::customers.account.organizations.create');
    }

    /**
     * Create a new organization for customer.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'inn' => 'required',
            'kpp' => 'nullable',
            'bank_name' => 'nullable',
            'bic' => 'nullable',
            'settlement_account' => 'nullable',
            'correspondent_account' => 'nullable',
        ]);

        $customer = auth()->guard('customer')->user();

        Event::dispatch('customer.organizations.create.before');

        $data = array_merge($request->only([
            'name',
            'inn',
            'kpp',
            'address',
            'bank_name',
            'bic',
            'settlement_account',
            'correspondent_account',
        ]), [
            'customer_id' => $customer->id,
        ]);

        $organization = $this->organizationRepository->create($data);

        Event::dispatch('customer.organizations.create.after', $organization);

        session()->flash('success', trans('shop::app.customers.account.organizations.create-success'));

        return redirect()->route('shop.customers.account.organizations.index');
    }

    /**
     * For editing the existing organizations of current logged in customer.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $organization = $this->organizationRepository->findOneWhere([
            'id' => $id,
            'customer_id' => auth()->guard('customer')->id(),
        ]);

        if (!$organization) {
            abort(404);
        }

        return view('shop::customers.account.organizations.edit')->with('organization', $organization);
    }

    /**
     * Update the organization.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(int $id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'inn' => 'required',
            'kpp' => 'nullable',
            'bank_name' => 'nullable',
            'bic' => 'nullable',
            'settlement_account' => 'nullable',
            'correspondent_account' => 'nullable',
        ]);

        $customer = auth()->guard('customer')->user();

        if (!$customer->organizations()->find($id)) {
            abort(401);
        }

        Event::dispatch('customer.organizations.update.before', $id);

        $data = $request->only([
            'name',
            'inn',
            'kpp',
            'address',
            'bank_name',
            'bic',
            'settlement_account',
            'correspondent_account',
        ]);

        $organization = $this->organizationRepository->update($data, $id);

        Event::dispatch('customer.organizations.update.after', $organization);

        session()->flash('success', trans('shop::app.customers.account.organizations.update-success'));

        return redirect()->route('shop.customers.account.organizations.index');
    }

    /**
     * Delete organization of the current customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $organization = $this->organizationRepository->findOneWhere([
            'id' => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (!$organization) {
            abort(404);
        }

        Event::dispatch('customer.organizations.delete.before', $id);

        $this->organizationRepository->delete($id);

        Event::dispatch('customer.organizations.delete.after', $id);

        session()->flash('success', trans('shop::app.customers.account.organizations.delete-success'));

        return redirect()->route('shop.customers.account.organizations.index');
    }

    /**
     * Lookup organization by INN.
     *
     * @param  string  $inn
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookupInn(string $inn)
    {
        $dadataHelper = app(\Webkul\Core\Helpers\Dadata\DadataHelper::class);

        $result = $dadataHelper->lookupOrganization($inn);

        if (!$result) {
            return response()->json([
                'message' => 'Организация не найдена',
            ], 404);
        }

        return response()->json($result);
    }
}
