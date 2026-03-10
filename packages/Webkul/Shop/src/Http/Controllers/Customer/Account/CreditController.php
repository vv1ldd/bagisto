<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\BlockchainSyncService;
use Webkul\Customer\Repositories\CustomerTransactionRepository;
use Webkul\Customer\Repositories\OrganizationRepository;
use Webkul\Core\Repositories\BillingEntityRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Webkul\Shop\Mail\Customer\TopupInvoiceNotification;

class CreditController extends Controller
{
    public function __construct(
        protected BlockchainSyncService $syncService,
        protected CustomerTransactionRepository $customerTransactionRepository,
        protected OrganizationRepository $organizationRepository,
        protected BillingEntityRepository $billingEntityRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();

        // Trigger on-demand deposit sync (rate-limited internally)
        $this->syncService->syncCustomerDeposits($customer);

        $verifiedAddresses = $customer
            ->crypto_addresses()
            ->whereNotNull('verified_at')
            ->orderBy('network')
            ->get();

        $allAddresses = $customer
            ->crypto_addresses()
            ->orderBy('network')
            ->get();

        $transactions = $customer
            ->credits()
            ->orderBy('id', 'desc')
            ->paginate(20);

        $organizations = $customer->organizations;

        $defaultBillingEntityId = core()->getConfigData('customer.settings.b2b.default_billing_entity');
        $defaultBillingEntity = $this->billingEntityRepository->find($defaultBillingEntityId);

        return view('shop::customers.account.credits.index', compact('verifiedAddresses', 'allAddresses', 'transactions', 'organizations', 'defaultBillingEntity'));
    }

    /**
     * Redirect to unified index with transactions step.
     */
    public function transactions()
    {
        return redirect()->route('shop.customers.account.credits.index', ['step' => 'transactions']);
    }

    /**
     * Redirect to unified index with deposit step.
     */
    public function deposit()
    {
        return redirect()->route('shop.customers.account.credits.index', ['step' => 'deposit']);
    }

    /**
     * Store a new top-up request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTopup()
    {
        try {
            $defaultBillingEntityId = core()->getConfigData('customer.settings.b2b.default_billing_entity');

            if (!$defaultBillingEntityId) {
                $defaultBillingEntityId = $this->billingEntityRepository->all()->first()?->id;
            }

            if (!$defaultBillingEntityId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No billing entity configured for top-ups.'
                ], 400);
            }

            $this->validate(request(), [
                'amount' => 'required|numeric|min:0.01',
                'organization_id' => 'required|exists:customer_organizations,id',
            ]);

            $customer = auth()->guard('customer')->user();

            $transaction = $this->customerTransactionRepository->create([
                'customer_id' => $customer->id,
                'amount' => request('amount'),
                'type' => 'deposit',
                'status' => 'pending',
                'notes' => 'Top-up via B2B Bank Transfer',
                'metadata' => [
                    'organization_id' => request('organization_id'),
                    'billing_entity_id' => $defaultBillingEntityId,
                ],
                'currency_code' => core()->getCurrentCurrencyCode(),
            ]);

            try {
                $organization = $this->organizationRepository->find($transaction->metadata['organization_id']);
                $billingEntity = $this->billingEntityRepository->find($transaction->metadata['billing_entity_id']);

                $pdf = Pdf::loadView('shop::customers.account.credits.topup-pdf', [
                    'transaction' => $transaction,
                    'organization' => $organization,
                    'billingEntity' => $billingEntity,
                ]);

                Mail::queue(new TopupInvoiceNotification($transaction, $pdf->output()));
            } catch (\Exception $e) {
                report($e);
            }

            return response()->json([
                'success' => true,
                'message' => trans('shop::app.customers.account.topup.success'),
                'transaction_id' => $transaction->id,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print proforma invoice for top-up.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printTopupInvoice($id)
    {
        $transaction = $this->customerTransactionRepository->findOrFail($id);

        if ($transaction->customer_id != auth()->guard('customer')->id()) {
            abort(403);
        }

        $organization = $this->organizationRepository->find($transaction->metadata['organization_id']);
        $billingEntity = $this->billingEntityRepository->find($transaction->metadata['billing_entity_id']);

        $pdf = PDF::loadView('shop::customers.account.credits.topup-pdf', [
            'transaction' => $transaction,
            'organization' => $organization,
            'billingEntity' => $billingEntity,
        ]);

        return $pdf->download('proforma-invoice-' . $transaction->id . '.pdf');
    }
}
