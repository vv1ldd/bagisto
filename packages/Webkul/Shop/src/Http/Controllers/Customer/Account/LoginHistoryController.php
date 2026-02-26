<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Support\Facades\Auth;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerLoginLogRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class LoginHistoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerLoginLogRepository  $customerLoginLogRepository
     * @return void
     */
    public function __construct(protected CustomerLoginLogRepository $customerLoginLogRepository)
    {
    }

    /**
     * Display the login activity and active sessions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();
        $lifetime = config('session.lifetime') ?? 120; // minutes

        $activeSessions = $this->customerLoginLogRepository
            ->scopeQuery(function ($query) use ($customer, $lifetime) {
                return $query->where('customer_id', $customer->id)
                    ->where('logged_out_at', null)
                    ->where('last_active_at', '>=', now()->subMinutes($lifetime));
            })->get();

        $loginHistory = $this->customerLoginLogRepository
            ->scopeQuery(function ($query) use ($customer) {
                return $query->where('customer_id', $customer->id)
                    ->orderBy('created_at', 'desc');
            })->paginate(10);

        return view('shop::customers.account.login-activity.index', compact('activeSessions', 'loginHistory'));
    }

    /**
     * Terminate a specific session.
     *
     * @param  string  $sessionId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = auth()->guard('customer')->user();
        $log = $this->customerLoginLogRepository->findOneWhere([
            'id' => $id,
            'customer_id' => $customer->id,
        ]);

        if (!$log) {
            session()->flash('error', trans('shop::app.customers.account.login-activity.revoke-failed'));
            return redirect()->back();
        }

        $sessionId = $log->session_id;

        // 1. Mark as logged out in our history
        $this->customerLoginLogRepository->update(['logged_out_at' => now()], $id);

        // 2. Clear Laravel session if possible
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('id', $sessionId)
                ->delete();
        }

        // 3. If it's the current session, log the user out
        if ($id == session('customer_login_log_id') || $sessionId === session()->getId()) {
            auth()->guard('customer')->logout();
            return redirect()->route('shop.customer.session.index');
        }

        session()->flash('success', trans('shop::app.customers.account.login-activity.revoked'));

        return redirect()->back();
    }
}
