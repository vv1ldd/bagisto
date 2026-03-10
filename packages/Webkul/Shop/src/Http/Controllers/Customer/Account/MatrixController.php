<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;

class MatrixController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CustomerRepository $customerRepository)
    {
    }

    /**
     * Redirect to the Matrix Web Client or deep link to Element X.
     */
    public function redirect()
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return redirect()->route('shop.customer.session.index');
        }

        // Generate a Matrix specific deep link or redirect to Element Web
        // Assuming Element X responds to matrix: URI scheme
        $matrixDomain = env('MATRIX_DOMAIN', 'matrix.meanly.com');

        // Deep link to a specific room or generic app launch
        $deepLink = "https://app.element.io/#/login";

        return redirect()->away($deepLink);
    }

    /**
     * A simulated endpoint for an external Matrix Auth Service (MAS) webhook
     * or a custom Synapse password provider to verify credentials.
     * 
     * In a real production setup, installing Laravel Passport for full OIDC
     * is recommended.
     */
    public function verifyCredentials(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->guard('customer')->attempt($credentials)) {
            $customer = auth()->guard('customer')->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $customer->id,
                    'username' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $customer->first_name . $customer->last_name)) . $customer->id,
                    'display_name' => $customer->name,
                ]
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
    }
}
