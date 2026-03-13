<?php

namespace Webkul\Shop\Services\Matrix;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatrixService
{
    protected $homeserverUrl;
    protected $sharedSecret;

    public function __construct()
    {
        $this->homeserverUrl = rtrim(config('services.matrix.homeserver_url'), '/');
        $this->sharedSecret = config('services.matrix.registration_shared_secret');
    }

    /**
     * Get or create a Matrix user for the given Bagisto customer.
     */
    public function getOrCreateUser($customer)
    {
        $username = $this->generateUsername($customer);
        
        // Try to register (Synapse will return 400 if user exists, which is fine)
        $this->registerUser($username);

        return [
            'username'   => "@{$username}:" . parse_url($this->homeserverUrl, PHP_URL_HOST),
            'password'   => $this->getDeterministicPassword($customer),
            'homeserver' => $this->homeserverUrl,
        ];
    }

    protected function generateUsername($customer)
    {
        // Simple username: customer_{id}
        return "customer_" . $customer->id;
    }

    protected function getDeterministicPassword($customer)
    {
        // Deterministic password based on customer ID and shared secret
        return hash_hmac('sha256', $customer->id, $this->sharedSecret);
    }

    protected function registerUser($username)
    {
        if (!$this->sharedSecret) {
            Log::error("Matrix registration failed: MATRIX_REGISTRATION_SHARED_SECRET not set.");
            return;
        }

        $nonceResponse = Http::get("{$this->homeserverUrl}/_matrix/client/v3/admin/register/nonce");
        $nonce = $nonceResponse->json('nonce');

        if (!$nonce) {
            Log::error("Matrix registration failed: Could not get nonce from Synapse.");
            return;
        }

        $password = $this->getDeterministicPassword(auth()->guard('customer')->user());
        
        // MAC = hmac_sha1(nonce + "\x00" + user + "\x00" + password + "\x00" + admin/user, shared_secret)
        $mac = hash_hmac('sha1', "{$nonce}\x00{$username}\x00{$password}\x00notadmin", $this->sharedSecret);

        $registrationResponse = Http::post("{$this->homeserverUrl}/_matrix/client/v3/admin/register", [
            'nonce'    => $nonce,
            'username' => $username,
            'password' => $password,
            'mac'      => $mac,
            'admin'    => false,
        ]);

        if (!$registrationResponse->successful() && $registrationResponse->json('errcode') !== 'M_USER_IN_USE') {
            Log::error("Matrix registration failed for user {$username}: " . $registrationResponse->body());
        }
    }
}
