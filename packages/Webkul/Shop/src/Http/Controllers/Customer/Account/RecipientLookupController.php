<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Models\Customer;

class RecipientLookupController extends Controller
{
    /**
     * Look up a recipient by alias.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lookup(Request $request)
    {
        $alias = $request->query('alias');

        if (! $alias) {
            return response()->json(['error' => 'Missing alias'], 400);
        }

        // Remove @ if present
        if (str_starts_with($alias, '@')) {
            $alias = substr($alias, 1);
        }

        $customer = Customer::where('credits_alias', $alias)->first();

        if (! $customer || ! $customer->credits_id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'address' => $customer->credits_id,
            'name'    => $customer->first_name . ' ' . $customer->last_name,
        ]);
    }
}
