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
            'caller_name'      => 'required|string|max:255',
            'caller_email'     => 'required|email',
            'recipient_emails' => 'required|array|min:1',
            'recipient_emails.*' => 'email',
        ]);

        $recipientEmails = array_values(array_unique(request('recipient_emails')));
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
        $session = CallSession::where('uuid', $uuid)->firstOrFail();
        $hash = request('h');
        $customer = auth()->guard('customer')->user();
        $cookieName = 'call_token_' . $uuid;
        $browserToken = request()->cookie($cookieName);

        if (!$hash && !$customer && !$browserToken) {
            return abort(403, 'Invitation hash or valid token is required.');
        }

        // 1. Identify Identity & Validate Hash
        // Creator can join if they have the hash, are auth creator, or have a creator cookie (if we add one)
        // For simplicity, creators don't need cookies as they have auth or email-hash
        $isValidCreator = ($hash === md5($session->caller_email . $uuid)) 
                        || ($customer && $customer->email === $session->caller_email);
        
        $recipients = $session->metadata['all_recipients'] ?? [$session->recipient_email];
        $isValidGuestRequest = false;
        
        // Guest check via hash or auth
        foreach ($recipients as $email) {
            if (($hash === md5($email . $uuid)) || ($customer && $customer->email === $email)) {
                $isValidGuestRequest = true;
                break;
            }
        }

        if (!$isValidCreator && !$isValidGuestRequest && !$browserToken) {
            return abort(403, 'Invalid or expired invitation link.');
        }

        // 2. Cookie-Based One-Time "Claim" Logic for Guests
        if (!$isValidCreator) {
            $metadata = $session->metadata ?? [];
            $claimedToken = $metadata['claimed_guest_token'] ?? null;

            // DEVICE HANDOVER: If they have a valid link hash, they can "RE-CLAIM" the slot 🕵️‍♂️📲🔄🚀
            // This allows switching from PC to Phone using the same link.
            if ($isValidGuestRequest || !$claimedToken) {
                // Generative or Refreshative token
                $newToken = Str::random(60);
                $metadata['claimed_guest_token'] = $newToken;
                $metadata['claimed_at'] = now()->toDateTimeString();
                
                $session->metadata = $metadata;
                $session->save();

                // Set cookie for 24 hours
                \Illuminate\Support\Facades\Cookie::queue($cookieName, $newToken, 1440);
            } else {
                // They don't have a valid hash AND the browser token doesn't match the claim
                if ($browserToken !== $claimedToken) {
                    return abort(403, 'This invitation link has already been used by another participant. Only 1-on-1 calls are permitted.');
                }
            }
        }

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
            $hash = md5($email . $uuid);
            Mail::to($email)->send(new GuestCallInvitation($callerName, $callUrl . '?h=' . $hash));
            
            return response()->json(['message' => 'Приглашение отправлено ' . $email]);
        } catch (\Exception $e) {
            Log::error('Guest Call Invite Error: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка при отправке приглашения'], 500);
        }
    }
}
