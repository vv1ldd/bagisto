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

    /**
     * Handle WebRTC signaling between users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signal()
    {
        $request = request();

        $request->validate([
            'to_user_id' => 'required|integer',
            'signal_data' => 'required|array',
        ]);

        $toUserId = $request->input('to_user_id');
        $signalData = $request->input('signal_data');
        $fromUserId = auth()->guard('customer')->id();

        if (!$fromUserId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        try {
            \Log::info('WebRTC Signal Trace:', ['from' => $fromUserId, 'to' => $toUserId, 'type' => $signalData['type'] ?? 'unknown']);

            event(new \Webkul\Shop\Events\CallSignal($toUserId, $fromUserId, $signalData));

            // If this is an initial call (offer), send an email notification to the recipient
            if (isset($signalData['type']) && $signalData['type'] === 'offer') {
                $recipient = $this->customerRepository->find($toUserId);
                $caller = auth()->guard('customer')->user();
                $callerName = $caller->username ?? $caller->first_name;

                \Log::info('Attempting to send call notification:', [
                    'recipient' => $recipient?->email,
                    'caller'    => $callerName
                ]);

                if ($recipient && $recipient->email) {
                    $recipient->notify(new \App\Notifications\CallInvitationNotification(
                        $callerName,
                        $fromUserId
                    ));
                    \Log::info('Notification dispatched to ' . $recipient->email);
                } else {
                    \Log::warning('Recipient not found or has no email:', ['id' => $toUserId]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('WebRTC Signaling Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка бродкаста: ' . $e->getMessage() . ' (Убедитесь, что настроен Pusher в .env)'
            ], 500);
        }

        return response()->json(['status' => 'success']);
    }
}
