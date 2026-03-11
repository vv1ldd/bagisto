<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\Request;
use Webkul\Shop\Events\CallSignal;
use Webkul\Customer\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Log;
use App\Notifications\CallInvitationNotification;

class CallController extends APIController
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
     * Send signal to target user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signal(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|integer',
            'signal_data' => 'required|array',
        ]);

        $toUserId = $request->to_user_id;
        $signalData = $request->signal_data;
        $fromUserId = auth()->guard('customer')->id();

        try {
            Log::info('WebRTC: Signal Received (API)', [
                'from_user_id' => $fromUserId,
                'to_user_id'   => $toUserId,
                'signal_type'  => $signalData['type'] ?? 'unknown'
            ]);

            broadcast(new CallSignal(
                $toUserId,
                $fromUserId,
                $signalData
            ))->toOthers();

            // If this is an initial call (offer), send an email notification to the recipient
            if (isset($signalData['type']) && $signalData['type'] === 'offer') {
                $recipient = $this->customerRepository->find($toUserId);
                $caller = auth()->guard('customer')->user();
                $callerName = $caller->username ?? $caller->first_name;

                Log::info('WebRTC: Offer detected (API), preparing email notification', [
                    'recipient_id'    => $toUserId,
                    'recipient_email' => $recipient?->email,
                    'caller_name'     => $callerName
                ]);

                if ($recipient && $recipient->email) {
                    try {
                        $recipient->notify(new CallInvitationNotification(
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
            Log::error('WebRTC Signaling Error (API): ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка сигналинга: ' . $e->getMessage()
            ], 500);
        }

        return response()->json(['success' => true]);
    }
}
