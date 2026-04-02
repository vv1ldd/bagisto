<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Services\MatrixService;
use Webkul\Customer\Repositories\HandshakeRepository;

class MatrixController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param  \Webkul\Customer\Services\MatrixService  $matrixService
     * @param  \Webkul\Customer\Repositories\HandshakeRepository  $handshakeRepository
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected MatrixService $matrixService,
        protected HandshakeRepository $handshakeRepository
    ) {
    }

    /**
     * Show the Hydrogen chat interface.
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();

        if (! $customer->is_matrix_enabled) {
            abort(403, 'Matrix chat is not enabled for your account.');
        }

        // Ensure user is registered on Matrix
        if (! $customer->matrix_access_token) {
            $this->matrixService->registerCustomer($customer);
        }

        return view('shop::customers.account.chat.index', [
            'customer' => $customer,
        ]);
    }

    /**
     * Synchronize Matrix accounts and rooms for accepted handshakes.
     */
    public function sync()
    {
        $customer = auth()->guard('customer')->user();

        if (! $customer->is_matrix_enabled) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Matrix chat is not enabled for your account.',
            ], 403);
        }

        // 1. Ensure current user is registered
        if (! $customer->matrix_access_token) {
            $this->matrixService->registerCustomer($customer);
        }

        // 2. Find all accepted handshakes without a room
        $handshakes = $this->handshakeRepository->findWhere([
            'status' => 'accepted',
        ])->filter(function ($handshake) use ($customer) {
            return ($handshake->sender_id == $customer->id || $handshake->receiver_id == $customer->id) 
                && is_null($handshake->matrix_room_id);
        });

        foreach ($handshakes as $handshake) {
            // Ensure both parties have Matrix IDs (receiver might not have logged in yet)
            $receiver = $handshake->sender_id == $customer->id ? $handshake->receiver : $handshake->sender;
            
            if (!$receiver->matrix_user_id) {
                $this->matrixService->registerCustomer($receiver);
            }

            $this->matrixService->createPeerRoom($handshake);
        }

        return response()->json([
            'status' => 'success',
            'matrix_id' => $customer->matrix_user_id,
        ]);
    }
}
