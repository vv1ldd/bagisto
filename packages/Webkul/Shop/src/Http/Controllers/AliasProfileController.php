<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Customer\Services\RecipientLookupService;

class AliasProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  RecipientLookupService  $recipientLookupService
     * @return void
     */
    public function __construct(
        protected RecipientLookupService $recipientLookupService
    ) {
    }

    /**
     * Display the public profile of a user by alias.
     *
     * @param  string  $alias
     * @return \Illuminate\View\View
     */
    public function index($alias)
    {
        // Ensure alias starts with @ for internal lookup consistency if needed, 
        // but our service handles both. Let's prepend @ if missing for the service call.
        $lookupAlias = str_starts_with($alias, '@') ? $alias : "@{$alias}";

        $customer = $this->recipientLookupService->find($lookupAlias);

        if (!$customer) {
            abort(404, 'User not found');
        }

        $cryptoAddresses = $customer->crypto_addresses()->where('is_active', 1)->get();

        return view('shop::customers.account.profile.view', compact('customer', 'cryptoAddresses'));
    }
}
