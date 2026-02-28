<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $transactions = auth()->guard('customer')->user()
            ->transactions()
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('shop::customers.account.transactions.index', compact('transactions'));
    }
}
