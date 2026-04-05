<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Webkul\Customer\Models\CallSession;
use Webkul\Customer\Services\RecipientLookupService;
use Webkul\Shop\Events\RoomCallSignal;
use Webkul\Shop\Mail\GuestCallInvitation;

class GuestCallController extends Controller
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
     * Show the form to start a guest call.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Disable legacy guest call creation in favor of Viral Onboarding
        return redirect()->route('shop.home.index');
    }

    /**
     * Start a new guest call session and send invitations.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'caller_name'      => 'required|string|max:255',
            'caller_email'     => 'required|email',
            'recipient_emails' => 'required|array|min:1',
            'recipient_emails.*' => 'required|string',
        ]);

        $recipientInputs = array_values(array_unique(request('recipient_emails')));
        $recipientEmails = [];

        foreach ($recipientInputs as $input) {
            $email = trim($input);

            // If it's not a valid email, try looking it up as an alias/Credits ID
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $customer = $this->recipientLookupService->find($email);

                if ($customer) {
                    $email = $customer->email;
                } else {
                    // If not found and not an email, we skip it or could return error.
                    // For now, let's just skip invalid identifiers to avoid Mail errors.
                    continue;
                }
            }

            $recipientEmails[] = $email;
        }

        if (empty($recipientEmails)) {
            session()->flash('error', 'Указанный получатель не найден. Пожалуйста, проверьте email или @alias.');
            return redirect()->back()->withInput();
        }

        $uuid = (string) Str::uuid();

        $session = CallSession::create([
            'uuid'            => $uuid,
            'caller_name'     => request('caller_name'),
            'caller_email'    => request('caller_email'),
            'recipient_email' => $recipientEmails[0], // Primary recipient
            'status'          => 'active',
            'metadata'        => [
                'all_recipients' => $recipientEmails,
            ]
        ]);

        $callUrl = route('shop.call.index', $uuid);

        try {
            // Send to all recipients
            foreach ($recipientEmails as $email) {
                $hash = md5($email . $uuid);
                Mail::to($email)->queue(new GuestCallInvitation(
                    request('caller_name'),
                    $callUrl . '?h=' . $hash
                ));
            }

            // Send to caller as well
            $callerHash = md5(request('caller_email') . $uuid);
            Mail::to(request('caller_email'))->queue(new GuestCallInvitation(
                'Система Meanly',
                $callUrl . '?h=' . $callerHash
            ));

            session()->flash('success', count($recipientEmails) . ' приглашений отправлено!');
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', 'Не удалось отправить часть приглашений, но сессия создана: ' . $callUrl);
        }

        $callerHash = md5(request('caller_email') . $uuid);
        return redirect()->route('shop.call.index', ['uuid' => $uuid, 'h' => $callerHash]);
    }

    /**
     * Show the guest call room.
     *
     * @param  string  $uuid
     * @return \Illuminate\View\View
     */
    public function index($uuid)
    {
        // Redirect legacy guest calls to the new Viral Onboarding Lobby 🕵️‍♂️🚀
        return redirect()->route('shop.meetings.join', ['room_uuid' => $uuid]);
    }

    /**
     * Handle WebRTC signaling for the room.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function signal($uuid)
    {
        $request = request();

        $request->validate([
            'signal_data' => 'required|array',
            'sender_name' => 'nullable|string',
        ]);

        $signalData = $request->input('signal_data');
        $senderName = $request->input('sender_name', 'Гость');

        try {
            event(new RoomCallSignal($uuid, $senderName, $signalData));
        } catch (\Exception $e) {
            Log::error('WebRTC Room Signaling Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Invite a guest to an existing call session via email.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function invite($uuid)
    {
        $session = CallSession::where('uuid', $uuid)->firstOrFail();

        $this->validate(request(), [
            'email' => 'required|email',
        ]);

        $email = request('email');
        $callUrl = route('shop.call.index', $uuid);
        $callerName = auth()->guard('customer')->check() 
            ? (auth()->guard('customer')->user()->first_name . ' ' . auth()->guard('customer')->user()->last_name)
            : ($session->caller_name ?? 'Гость');

        try {
            $hash = md5($email . $uuid);
            Mail::to($email)->send(new GuestCallInvitation($callerName, $callUrl . '?h=' . $hash));
            
            return response()->json(['message' => 'Приглашение отправлено ' . $email]);
        } catch (\Exception $e) {
            Log::error('Guest Call Invite Error: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при отправке приглашения'], 500);
        }
    }
}
