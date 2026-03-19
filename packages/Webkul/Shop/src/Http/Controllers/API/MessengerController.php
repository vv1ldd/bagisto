<?php

namespace Webkul\Shop\Http\Controllers\API;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Services\Matrix\MatrixService;

class MessengerController extends Controller
{
    public function __construct(
        protected MatrixService $matrixService
    ) {}

    public function getCredentials()
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $credentials = $this->matrixService->getOrCreateUser($customer);
            return response()->json($credentials);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
