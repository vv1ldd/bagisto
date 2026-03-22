<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\BlockchainSyncService;
use Webkul\Customer\Repositories\CustomerTransactionRepository;
use Webkul\Customer\Repositories\OrganizationRepository;
use Webkul\Core\Repositories\BillingEntityRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Webkul\Shop\Mail\Customer\InvoiceNotification;
use Webkul\Shop\Helpers\NumberToWords;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

        // Combine Credits (Transactions) and Orders
        $credits = $customer->credits()->get()->map(function ($item) {
            $item->merged_type = 'transaction';
            return $item;
        });

        $orders = $customer->orders()->get()->map(function ($item) {
            $item->merged_type = 'order';
            return $item;
        });

        $mergedCollection = $credits->concat($orders)->sortByDesc('created_at');

        // Manual Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $mergedCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $transactions = new LengthAwarePaginator($currentItems, $mergedCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $organizations = $customer->organizations;

        $defaultBillingEntity = $this->billingEntityRepository->skipCache()->getDefault();

        $allAssets = [
            'bitcoin' => ['icon' => '₿'],
            'ethereum' => ['icon' => 'Ξ'],
            'ton' => ['icon' => '💎'],
            'usdt_ton' => ['icon' => '₮'],
            'dash' => ['icon' => 'Đ']
        ];

        return view('shop::customers.account.credits.index', compact('verifiedAddresses', 'allAddresses', 'transactions', 'organizations', 'defaultBillingEntity', 'allAssets'));
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
    public function storeInvoice()
    {
        try {
            $billingEntity = $this->billingEntityRepository->skipCache()->getDefault();

            if (!$billingEntity) {
                return response()->json([
                    'success' => false,
                    'message' => 'No billing entity configured for top-ups.'
                ], 400);
            }

            $defaultBillingEntityId = $billingEntity->id;

            $this->validate(request(), [
                'amount' => 'required|numeric|min:0.01',
                'organization_id' => 'required|exists:customer_organizations,id',
            ]);

            $customer = auth()->guard('customer')->user();

            $transaction = $this->customerTransactionRepository->create([
                'uuid' => (string) Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => request('amount'),
                'type' => 'deposit',
                'status' => 'pending',
                'notes' => 'Услуги в области информационных технологий',
                'metadata' => [
                    'organization_id' => request('organization_id'),
                    'billing_entity_id' => $defaultBillingEntityId,
                ],
                'currency_code' => core()->getCurrentCurrencyCode(),
            ]);

            try {
                $billingEntity = $this->billingEntityRepository->find($defaultBillingEntityId);

                $organization = $this->organizationRepository->find(request('organization_id'));

                $amountInWords = NumberToWords::convert($transaction->amount);

                $amountInKopeks = (int) ($transaction->amount * 100);
                $qrCodeData = "ST00012|Name={$billingEntity->name}|PersonalAcc={$billingEntity->settlement_account}|BankName={$billingEntity->bank_name}|BIC={$billingEntity->bic}|CorrespAcc={$billingEntity->correspondent_account}|PayeeINN={$billingEntity->inn}|PayeeKPP={$billingEntity->kpp}|Sum={$amountInKopeks}|Purpose=Оплата по счету №{$transaction->id} от {$transaction->created_at->format('d.m.Y')}";

                $pdf = Pdf::loadView('shop::customers.account.credits.invoice-pdf', compact('transaction', 'organization', 'billingEntity', 'amountInWords', 'qrCodeData'));

                Mail::queue(new InvoiceNotification($transaction, $pdf->output()));
            } catch (\Exception $e) {
                report($e);
            }

            return response()->json([
                'success' => true,
                'message' => trans('shop::app.customers.account.invoice.success'),
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
    public function printInvoice($id)
    {
        $transaction = $this->customerTransactionRepository->findOrFail($id);

        if ($transaction->customer_id != auth()->guard('customer')->id()) {
            abort(403);
        }

        $organization = $this->organizationRepository->find($transaction->metadata['organization_id']);
        $billingEntity = $this->billingEntityRepository->find($transaction->metadata['billing_entity_id']);

        $amountInWords = NumberToWords::convert($transaction->amount);

        $amountInKopeks = (int) ($transaction->amount * 100);
        $qrCodeData = "ST00012|Name={$billingEntity->name}|PersonalAcc={$billingEntity->settlement_account}|BankName={$billingEntity->bank_name}|BIC={$billingEntity->bic}|CorrespAcc={$billingEntity->correspondent_account}|PayeeINN={$billingEntity->inn}|PayeeKPP={$billingEntity->kpp}|Sum={$amountInKopeks}|Purpose=Оплата по счету №{$transaction->id} от {$transaction->created_at->format('d.m.Y')}";

        $pdf = Pdf::loadView('shop::customers.account.credits.invoice-pdf', [
            'transaction' => $transaction,
            'organization' => $organization,
            'billingEntity' => $billingEntity,
            'amountInWords' => $amountInWords,
            'qrCodeData' => $qrCodeData,
        ]);

        $fileName = 'Счет_Оферта_' . $transaction->id . '_от_' . $transaction->created_at->format('d.m.Y') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Re-send proforma invoice via email.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailInvoice($id)
    {
        try {
            $transaction = $this->customerTransactionRepository->findOrFail($id);

            if ($transaction->customer_id != auth()->guard('customer')->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $organization = $this->organizationRepository->find($transaction->metadata['organization_id']);
            $billingEntity = $this->billingEntityRepository->find($transaction->metadata['billing_entity_id']);

            $amountInWords = NumberToWords::convert($transaction->amount);

            $amountInKopeks = (int) ($transaction->amount * 100);
            $qrCodeData = "ST00012|Name={$billingEntity->name}|PersonalAcc={$billingEntity->settlement_account}|BankName={$billingEntity->bank_name}|BIC={$billingEntity->bic}|CorrespAcc={$billingEntity->correspondent_account}|PayeeINN={$billingEntity->inn}|PayeeKPP={$billingEntity->kpp}|Sum={$amountInKopeks}|Purpose=Оплата по счету №{$transaction->id} от {$transaction->created_at->format('d.m.Y')}";

            $pdf = Pdf::loadView('shop::customers.account.credits.invoice-pdf', compact('transaction', 'organization', 'billingEntity', 'amountInWords', 'qrCodeData'));

            // Use queue to prevent UI hang, base64 encode PDF to prevent JSON serialization errors
            Mail::queue(new InvoiceNotification($transaction, base64_encode($pdf->output())));

            return response()->json([
                'success' => true,
                'message' => 'Invoice queued for sending successfully.'
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bank accounts for an organization.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBankAccounts($id)
    {
        $organization = $this->organizationRepository->find($id);

        if (!$organization || $organization->customer_id != auth()->guard('customer')->id()) {
            return response()->json([], 403);
        }

        return response()->json($organization->bank_accounts);
    }
}
