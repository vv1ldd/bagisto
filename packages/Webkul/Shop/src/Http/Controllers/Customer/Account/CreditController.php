<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $transactions = auth()->guard('customer')->user()
            ->credits()
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('shop::customers.account.credits.index', compact('transactions'));
    }
}
