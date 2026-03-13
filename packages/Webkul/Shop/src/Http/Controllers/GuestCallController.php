<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Webkul\Customer\Models\CallSession;
use Webkul\Shop\Events\RoomCallSignal;
use Webkul\Shop\Mail\GuestCallInvitation;

class GuestCallController extends Controller
{
    /**
     * Show the form to start a guest call.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('shop::call.create');
    }

    /**
     * Start a new guest call session and send invitations.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'caller_name'     => 'required|string|max:255',
            'caller_email'    => 'required|email',
            'recipient_email' => 'required|email',
        ]);

        $uuid = (string) Str::uuid();

        $session = CallSession::create([
            'uuid'            => $uuid,
            'caller_name'     => request('caller_name'),
            'caller_email'    => request('caller_email'),
            'recipient_email' => request('recipient_email'),
            'status'          => 'active',
        ]);

        $callUrl = route('shop.call.index', $uuid);

        try {
            // Send to recipient
            Mail::to(request('recipient_email'))->queue(new GuestCallInvitation(
                request('caller_name'),
                $callUrl . '?email=' . urlencode(request('recipient_email'))
            ));

            // Send to caller as well
            Mail::to(request('caller_email'))->queue(new GuestCallInvitation(
                'Система Meanly',
                $callUrl . '?email=' . urlencode(request('caller_email'))
            ));

            session()->flash('success', 'Приглашения отправлены! Проверьте почту для входа в звонок.');
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', 'Не удалось отправить приглашения, но сессия создана: ' . $callUrl);
        }

        return redirect()->route('shop.call.index', $uuid);
    }

    /**
     * Show the guest call room.
     *
     * @param  string  $uuid
     * @return \Illuminate\View\View
     */
    public function index($uuid)
    {
        $session = CallSession::where('uuid', $uuid)->firstOrFail();

        // If session is ended, we might want to check that, but for now allow re-entry
        
        return view('shop::call.index', compact('session'));
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
            Mail::to($email)->send(new GuestCallInvitation($callerName, $callUrl . '?email=' . urlencode($email)));
            
            return response()->json(['message' => 'Приглашение отправлено ' . $email]);
        } catch (\Exception $e) {
            Log::error('Guest Call Invite Error: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при отправке приглашения'], 500);
        }
    }
}
