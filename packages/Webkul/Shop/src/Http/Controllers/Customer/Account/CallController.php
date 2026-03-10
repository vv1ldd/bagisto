<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;

class CallController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @return void
     */
    public function __construct(protected CustomerRepository $customerRepository)
    {
    }

    /**
     * Display the calls page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer->is_call_enabled) {
            abort(403, 'P2P Calls are not enabled for your account.');
        }

        $contacts = [
            [
                'id' => 1,
                'name' => 'Поддержка',
                'type' => 'support',
                'description' => 'Техническая поддержка Meanly',
                'icon' => '🎧'
            ]
        ];

        if ($customer->is_investor) {
            $otherInvestors = $this->customerRepository->findWhere([
                ['is_investor', '=', 1],
                ['id', '<>', $customer->id],
                ['status', '=', 1]
            ]);

            foreach ($otherInvestors as $investor) {
                $contacts[] = [
                    'id' => $investor->id,
                    'name' => $investor->username ?? $investor->first_name,
                    'type' => 'investor',
                    'description' => 'Инвестор',
                    'icon' => '💎'
                ];
            }
        }

        return view('shop::customers.account.calls.index', compact('contacts'));
    }
}
