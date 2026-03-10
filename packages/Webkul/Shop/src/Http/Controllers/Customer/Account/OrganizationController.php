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
    public function __construct(
        protected OrganizationRepository $organizationRepository,
        protected \Webkul\Customer\Repositories\BankRepository $bankRepository
    ) {
    }

    /**
     * Organization route index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $organizations = $this->organizationRepository->findWhere([
            'customer_id' => auth()->guard('customer')->id(),
        ]);

        return view('shop::customers.account.organizations.index', compact('organizations'));
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
            'ogrn' => 'nullable',
            'bank_name' => 'nullable',
            'bic' => 'nullable',
            'settlement_account' => 'nullable',
            'correspondent_account' => 'nullable',
        ]);

        $customer = auth()->guard('customer')->user();

        $inn = $request->input('inn');
        $existing = $this->organizationRepository->findOneWhere([
            'customer_id' => $customer->id,
            'inn' => $inn,
        ]);

        if ($existing) {
            return back()->withErrors(['inn' => 'Организация с таким ИНН уже добавлена'])->withInput();
        }

        Event::dispatch('customer.organizations.create.before');

        $data = array_merge($request->only([
            'name',
            'inn',
            'kpp',
            'ogrn',
            'address',
            // Kept for backward compatibility
            'bank_name',
            'bic',
            'settlement_account',
            'correspondent_account',
        ]), [
            'customer_id' => $customer->id,
        ]);

        // Sanitize bank details before validation and storage
        $bic = preg_replace('/\D/', '', $request->input('bic', ''));
        $settlementAccount = preg_replace('/\D/', '', $request->input('settlement_account', ''));

        if (!empty($settlementAccount) && !empty($bic)) {
            if (!$this->isValidBankAccount($bic, $settlementAccount)) {
                return back()->withErrors(['settlement_account' => 'Неверный контрольный ключ расчетного счета для указанного БИК'])->withInput();
            }
        }

        $organization = $this->organizationRepository->create($data);

        // Store primary settlement account
        if (!empty($settlementAccount) && !empty($bic)) {
            $organization->settlementAccounts()->create([
                'bic' => $bic,
                'bank_name' => $request->input('bank_name'),
                'correspondent_account' => preg_replace('/\D/', '', $request->input('correspondent_account', '')),
                'settlement_account' => $settlementAccount,
                'is_default' => true,
            ]);
        }

        Event::dispatch('customer.organizations.create.after', $organization);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => trans('shop::app.customers.account.organizations.create-success'),
            ]);
        }

        session()->flash('success', trans('shop::app.customers.account.organizations.create-success'));

        if ($request->has('from_wallet')) {
            return redirect()->route('shop.customers.account.credits.index', ['step' => 'organizations']);
        }

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

        return view('shop::customers.account.organizations.edit', compact('organization'));
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
            'ogrn' => 'nullable',
            'bank_name' => 'nullable',
            'bic' => 'nullable',
            'settlement_account' => 'nullable',
            'correspondent_account' => 'nullable',
        ]);

        $customer = auth()->guard('customer')->user();
        $organization = $customer->organizations()->find($id);

        if (!$organization) {
            abort(401);
        }

        Event::dispatch('customer.organizations.update.before', $id);

        $data = $request->only([
            'name',
            'inn',
            'kpp',
            'ogrn',
            'address',
            // Kept for backward compatibility
            'bank_name',
            'bic',
            'settlement_account',
            'correspondent_account',
        ]);

        $organization = $this->organizationRepository->update($data, $id);

        // If the primary settlement account fields are updated, we'll try to update the default account, 
        // but for simplicity in this implementation, we rely on the specific `storeSettlementAccount` endpoint.

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

    /**
     * Lookup bank by BIC.
     *
     * @param  string  $bic
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookupBic(string $bic)
    {
        // 1. Check local cache first
        $bank = $this->bankRepository->findOneByField('bic', $bic);

        if ($bank) {
            return response()->json([
                'bank_name' => $bank->name,
                'correspondent_account' => $bank->correspondent_account,
                'bic' => $bank->bic,
            ]);
        }

        // 2. Fallback to DaData
        $dadataHelper = app(\Webkul\Core\Helpers\Dadata\DadataHelper::class);
        $result = $dadataHelper->lookupBank($bic);

        if (!$result) {
            return response()->json([
                'message' => 'Банк не найден. Проверьте БИК.',
            ], 404);
        }

        // 3. Cache the result for future
        $this->bankRepository->updateOrCreate([
            'bic' => $result['bic'],
        ], [
            'name' => $result['bank_name'],
            'correspondent_account' => $result['correspondent_account'],
        ]);

        return response()->json($result);
    }

    /**
     * Suggest bank by query (name, bic, swift).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestBank(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([], 400);
        }

        // 1. Search in local cache (by BIC or Name)
        $localResults = $this->bankRepository->scopeQuery(function ($queryBuilder) use ($query) {
            return $queryBuilder->where('bic', 'like', $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%');
        })->get();

        $results = $localResults->map(function ($bank) {
            return [
                'name' => $bank->name,
                'bank_name' => $bank->name,
                'correspondent_account' => $bank->correspondent_account,
                'bic' => $bank->bic,
                'address' => $bank->address,
            ];
        })->toArray();

        // 2. If results are insufficient, fetch from DaData
        if (count($results) < 5) {
            $dadataHelper = app(\Webkul\Core\Helpers\Dadata\DadataHelper::class);
            $dadataResults = $dadataHelper->suggestBank($query);

            foreach ($dadataResults as $item) {
                // Upsert into local cache
                $this->bankRepository->updateOrCreate([
                    'bic' => $item['bic'],
                ], [
                    'name' => $item['bank_name'],
                    'correspondent_account' => $item['correspondent_account'],
                    'address' => $item['address'],
                ]);

                // Merge into results if not already there (by BIC)
                $exists = false;
                foreach ($results as $res) {
                    if ($res['bic'] === $item['bic']) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $results[] = $item;
                }
            }
        }

        return response()->json(array_slice($results, 0, 15));
    }

    /**
     * Suggest organizations by query.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestOrganization(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([], 400);
        }

        $dadataHelper = app(\Webkul\Core\Helpers\Dadata\DadataHelper::class);

        $results = $dadataHelper->suggestOrganization($query);

        return response()->json($results);
    }

    /**
     * Store a new settlement account for a specific organization.
     */
    public function storeSettlementAccount(int $organizationId, Request $request)
    {
        $request->validate([
            'bic' => 'required',
            'bank_name' => 'required',
            'settlement_account' => 'required',
            'correspondent_account' => 'nullable',
            'alias' => 'nullable|string|max:255',
        ]);

        $organization = $this->organizationRepository->findOneWhere([
            'id' => $organizationId,
            'customer_id' => auth()->guard('customer')->id(),
        ]);

        if (!$organization) {
            abort(404);
        }

        // Sanitize bank details
        $bic = preg_replace('/\D/', '', $request->input('bic', ''));
        $settlementAccount = preg_replace('/\D/', '', $request->input('settlement_account', ''));

        if (!$this->isValidBankAccount($bic, $settlementAccount)) {
            return back()->withErrors(['settlement_account' => 'Неверный контрольный ключ расчетного счета'])->withInput();
        }

        // Check if this account already exists for the organization
        $existingAccount = $organization->settlementAccounts()->where([
            'bic' => $bic,
            'settlement_account' => $settlementAccount,
        ])->first();

        if ($existingAccount) {
            return back()->withErrors(['settlement_account' => 'Этот расчетный счет уже добавлен'])->withInput();
        }

        // Check if it's the first account
        $isDefault = $organization->settlementAccounts()->count() === 0;

        $organization->settlementAccounts()->create([
            'bic' => $bic,
            'bank_name' => $request->input('bank_name'),
            'settlement_account' => $settlementAccount,
            'correspondent_account' => preg_replace('/\D/', '', $request->input('correspondent_account', '')),
            'alias' => $request->input('alias'),
            'is_default' => $isDefault,
        ]);

        return back()->with('success', 'Расчетный счет успешно добавлен');
    }

    /**
     * Update the alias for a settlement account.
     */
    public function updateSettlementAccountAlias(int $organizationId, int $accountId, Request $request)
    {
        $request->validate([
            'alias' => 'nullable|string|max:255',
        ]);

        $organization = $this->organizationRepository->findOneWhere([
            'id' => $organizationId,
            'customer_id' => auth()->guard('customer')->id(),
        ]);

        if (!$organization) {
            abort(404);
        }

        $account = $organization->settlementAccounts()->find($accountId);
        if ($account) {
            $account->update([
                'alias' => $request->input('alias'),
            ]);
        }

        return back()->with('success', 'Название счета успешно обновлено');
    }

    /**
     * Delete a specific settlement account.
     */
    public function destroySettlementAccount(int $organizationId, int $accountId)
    {
        $organization = $this->organizationRepository->findOneWhere([
            'id' => $organizationId,
            'customer_id' => auth()->guard('customer')->id(),
        ]);

        if (!$organization) {
            abort(404);
        }

        $account = $organization->settlementAccounts()->find($accountId);
        if ($account) {
            $account->delete();
        }

        return back()->with('success', 'Расчетный счет успешно удален');
    }

    /**
     * Validate the bank account checksum according to CBR standard.
     *
     * @param  string  $bic
     * @param  string  $account
     * @return bool
     */
    protected function isValidBankAccount($bic, $account)
    {
        // Sanitize just in case
        $bic = preg_replace('/\D/', '', $bic);
        $account = preg_replace('/\D/', '', $account);

        if (strlen($bic) !== 9 || strlen($account) !== 20) {
            return false;
        }

        // RKC Rule: If digits 7 and 8 of BIC are '00', use '0' + 5th + 6th digits of BIC.
        // Index 6 is 7th digit, Index 7 is 8th digit.
        if ($bic[6] === '0' && $bic[7] === '0') {
            $bicPart = '0' . $bic[4] . $bic[5];
        } else {
            $bicPart = substr($bic, -3);
        }

        $combined = $bicPart . $account;
        $weights = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];

        $sum = 0;
        for ($i = 0; $i < 23; $i++) {
            $sum += ((int) $combined[$i] * $weights[$i]) % 10;
        }

        return $sum % 10 === 0;
    }
}
