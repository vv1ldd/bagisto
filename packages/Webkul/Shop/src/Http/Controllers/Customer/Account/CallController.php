<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Log;

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

        $contacts = [];

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
        $fromUser = auth()->guard('customer')->user();
        $fromUserId = $fromUser?->id;

        if (!$fromUserId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // Attach caller name for the frontend UI
        $signalData['caller_name'] = $fromUser->username ?? $fromUser->first_name ?? 'Пользователь';

        try {
            Log::info('WebRTC: Signal Received', [
                'from_user_id' => $fromUserId,
                'to_user_id'   => $toUserId,
                'signal_type'  => $signalData['type'] ?? 'unknown',
                'caller_name'  => $signalData['caller_name']
            ]);

            Log::info('WebRTC: Dispatching CallSignal event', [
                'to_user_id'   => $toUserId,
                'from_user_id' => $fromUserId
            ]);

            event(new \Webkul\Shop\Events\CallSignal($toUserId, $fromUserId, $signalData));

            Log::info('WebRTC: CallSignal event dispatched successfully');

            // If this is an initial call (offer), send an email notification to the recipient
            if (isset($signalData['type']) && $signalData['type'] === 'offer') {
                $recipient = $this->customerRepository->find($toUserId);
                $caller = auth()->guard('customer')->user();
                $callerName = $caller->username ?? $caller->first_name;

                Log::info('WebRTC: Offer detected, preparing email notification', [
                    'recipient_id'    => $toUserId,
                    'recipient_email' => $recipient?->email,
                    'caller_name'     => $callerName
                ]);

                if ($recipient && $recipient->email) {
                    try {
                        $recipient->notify(new \App\Notifications\CallInvitationNotification(
                            $callerName,
                            $fromUserId
                        ));
                        Log::info('WebRTC: Notification successfully sent to ' . $recipient->email);
                    } catch (\Exception $notifyException) {
                        Log::error('WebRTC: Notification failed to send: ' . $notifyException->getMessage());
                    }
                } else {
                    Log::warning('WebRTC: Cannot send notification. Recipient not found or has no email.', ['recipient_id' => $toUserId]);
                }
            }
        } catch (\Exception $e) {
            Log::error('WebRTC Signaling Error: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка бродкаста: ' . $e->getMessage() . ' (Убедитесь, что настроен Pusher в .env)'
            ], 500);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Update participant readiness state for a room.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ready()
    {
        $request = request();
        $request->validate([
            'room_uuid'  => 'required|string',
            'session_id' => 'required|string',
            'is_ready'   => 'required|boolean',
        ]);

        $roomUuid = $request->input('room_uuid');
        $sessionId = $request->input('session_id');
        $isReady = $request->input('is_ready');
        $user = auth()->guard('customer')->user();

        $cacheKey = "call_room_state_{$roomUuid}";
        $roomState = \Illuminate\Support\Facades\Cache::get($cacheKey, []);

        if ($isReady) {
            $roomState[$sessionId] = [
                'user_id'    => $user?->id,
                'name'       => $user->username ?? $user->first_name ?? 'Гость',
                'session_id' => $sessionId,
                'last_seen'  => time(),
            ];
        } else {
            unset($roomState[$sessionId]);
        }

        // Cleanup old sessions (older than 1 minute)
        $roomState = array_filter($roomState, fn($s) => $s['last_seen'] > (time() - 60));
        
        \Illuminate\Support\Facades\Cache::put($cacheKey, $roomState, 300); // 5 min TTL

        // CHECK IF BOTH ARE READY 🕵️‍♂️🔄🚀
        $readyParticipants = array_values($roomState);
        if (count($readyParticipants) >= 2) {
            // Deterministic roles (initiator / receiver)
            usort($readyParticipants, fn($a, $b) => strcmp($a['session_id'], $b['session_id']));
            
            $initiator = $readyParticipants[0];
            $receiver  = $readyParticipants[1];

            Log::info("WebRTC: State Sync v2 - Session Started for Room {$roomUuid}", [
                'initiator' => $initiator['session_id'],
                'receiver'  => $receiver['session_id'],
                'version'   => 1
            ]);

            // Fire the global room signal and tell everyone the roles
            event(new \Webkul\Shop\Events\RoomCallSignal($roomUuid, 'System', [
                'type'                 => 'session_started',
                'version'              => 1,
                'sessionId'            => $roomUuid, // Protocol: sessionId matches roomUuid
                'initiator_session_id' => $initiator['session_id'],
                'receiver_session_id'  => $receiver['session_id'],
                'participants'         => $readyParticipants,
                'timestamp'            => time() * 1000 // ms for JS compatibility
            ], 0)); // Pass 0 as System fromUserId
        }

        return response()->json([
            'status'       => 'success',
            'version'      => 1,
            'participants' => array_values($roomState)
        ]);
    }
}
