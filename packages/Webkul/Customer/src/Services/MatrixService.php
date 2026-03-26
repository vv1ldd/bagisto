<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\Handshake;

class MatrixService
{
    /**
     * @var string|null
     */
    protected $homeserverUrl;

    /**
     * @var string|null
     */
    protected $sharedSecret;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->homeserverUrl = rtrim(config('services.matrix.homeserver_url'), '/');
        $this->sharedSecret = config('services.matrix.registration_shared_secret');
    }

    /**
     * Register a customer on the Matrix homeserver.
     *
     * @param  \Webkul\Customer\Models\Customer  $customer
     * @return string|null Access Token
     */
    public function registerCustomer(Customer $customer)
    {
        if (!$this->sharedSecret) {
            Log::error('Matrix registration shared secret not configured.');
            return null;
        }

        $username = $customer->credits_id; // Using credits_id as username
        $password = bin2hex(random_bytes(32)); // Random password, not used by user (Passkey/Mnemonic based instead)
        $admin = false;

        // Shared Secret Registration HMAC
        $mac = hash_hmac('sha1', $username . "\0" . $password . "\0" . ($admin ? 'admin' : 'notadmin'), $this->sharedSecret);

        try {
            $response = Http::post("{$this->homeserverUrl}/_synapse/admin/v1/register", [
                'username' => $username,
                'password' => $password,
                'admin'    => $admin,
                'mac'      => $mac,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $customer->update([
                    'matrix_user_id'      => $data['user_id'],
                    'matrix_access_token' => $data['access_token'],
                ]);

                return $data['access_token'];
            }

            Log::error('Matrix registration failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Matrix registration error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a private E2EE room for a handshake.
     *
     * @param  \Webkul\Customer\Models\Handshake  $handshake
     * @return string|null Room ID
     */
    public function createPeerRoom(Handshake $handshake)
    {
        $sender = $handshake->sender;
        $receiver = $handshake->receiver;

        $accessToken = $sender->matrix_access_token ?? $this->registerCustomer($sender);

        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)->post("{$this->homeserverUrl}/_matrix/client/r0/createRoom", [
                'preset'     => 'private_chat',
                'name'       => "Chat with {$receiver->name}",
                'invite'     => [$receiver->matrix_id],
                'is_direct'  => true,
                'initial_state' => [
                    [
                        'type'    => 'm.room.encryption',
                        'state_key' => '',
                        'content' => [
                            'algorithm' => 'm.megolm.v1.aes-sha2',
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $roomId = $data['room_id'];

                $handshake->update(['matrix_room_id' => $roomId]);

                return $roomId;
            }

            Log::error('Matrix room creation failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Matrix room creation error: ' . $e->getMessage());
            return null;
        }
    }
}
