<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\Request;
use Webkul\Shop\Events\CallSignal;

class CallController extends APIController
{
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

        broadcast(new CallSignal(
            $request->to_user_id,
            auth()->guard('customer')->id(),
            $request->signal_data
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
