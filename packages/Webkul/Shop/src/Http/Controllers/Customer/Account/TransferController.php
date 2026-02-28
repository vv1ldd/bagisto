<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\RecipientLookupService;
use Webkul\Customer\Services\InternalTransferService;

class TransferController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  RecipientLookupService  $recipientLookupService
     * @param  InternalTransferService  $internalTransferService
     * @return void
     */
    public function __construct(
        protected RecipientLookupService $recipientLookupService,
        protected InternalTransferService $internalTransferService
    ) {
    }

    /**
     * Handle the transfer request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('error', 'Внутренние переводы временно отключены.');
    }
}
