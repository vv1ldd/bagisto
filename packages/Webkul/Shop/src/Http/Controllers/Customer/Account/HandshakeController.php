<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\HandshakeProxy;

class HandshakeController extends Controller
{
    /**
     * Display a listing of the handshakes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();

        $handshakes = HandshakeProxy::where('sender_id', $customer->id)
            ->orWhere('receiver_id', $customer->id)
            ->with(['sender', 'receiver'])
            ->get();

        return view('shop::customers.account.handshakes.index', compact('handshakes'));
    }

    /**
     * Initiate a handshake (SSH ping style).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ping(Request $request)
    {
        $request->validate([
            'target' => 'required|string', // can be alias or address
        ]);

        $sender = auth()->guard('customer')->user();
        $target = $request->input('target');

        // Remove @ if present for alias lookup
        if (str_starts_with($target, '@')) {
            $target = substr($target, 1);
        }

        // Find receiver
        $receiver = Customer::where('credits_alias', $target)
            ->orWhere('credits_id', $target)
            ->first();

        if (! $receiver) {
            return response()->json(['message' => 'Target not found.'], 404);
        }

        if ($receiver->id === $sender->id) {
            return response()->json(['message' => 'You cannot handshake yourself.'], 400);
        }

        // Check if handshake already exists
        $existing = $sender->getHandshakeWith($receiver->id);

        if ($existing) {
            return response()->json([
                'message' => 'Handshake already exists or is pending.',
                'status'  => $existing->status,
            ], 422);
        }

        // Create new pending handshake
        HandshakeProxy::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'status'      => 'pending',
        ]);

        return response()->json(['message' => 'Handshake pinged successfully. Waiting for acknowledgement.']);
    }

    /**
     * Acknowledge (accept) a handshake.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function acknowledge($id)
    {
        $customer = auth()->guard('customer')->user();

        $handshake = HandshakeProxy::where('id', $id)
            ->where('receiver_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if (! $handshake) {
            return response()->json(['message' => 'Handshake request not found or unauthorized.'], 404);
        }

        $handshake->update(['status' => 'accepted']);

        return response()->json(['message' => 'Handshake acknowledged. Connection established.']);
    }

    /**
     * Terminate (disconnect or decline) a handshake.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function terminate($id)
    {
        $customer = auth()->guard('customer')->user();

        $handshake = HandshakeProxy::where('id', $id)
            ->where(function ($query) use ($customer) {
                $query->where('sender_id', $customer->id)
                    ->orWhere('receiver_id', $customer->id);
            })
            ->first();

        if (! $handshake) {
            return response()->json(['message' => 'Handshake not found.'], 404);
        }

        $handshake->delete();

        return response()->json(['message' => 'Handshake terminated. Connection severed.']);
    }
}
