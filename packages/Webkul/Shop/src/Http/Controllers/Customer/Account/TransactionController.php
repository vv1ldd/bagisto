<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerTransactionRepository;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerTransactionRepository  $customerTransactionRepository
     * @return void
     */
    public function __construct(
        protected CustomerTransactionRepository $customerTransactionRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $transactions = $this->customerTransactionRepository->where([
            'customer_id' => auth()->guard('customer')->id(),
        ])->latest()->paginate(10);

        return view('shop::customers.account.transactions.index', compact('transactions'));
    }
}
