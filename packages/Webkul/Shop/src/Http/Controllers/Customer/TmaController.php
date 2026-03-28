<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Shop\Http\Controllers\Controller;

class TmaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected \Webkul\Customer\Services\MnemonicService $mnemonicService,
        protected \Webkul\Customer\Services\BlockchainAddressService $blockchainAddressService
    ) {
    }

    /**
     * Authenticate or register a user via Telegram Mini App initData.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $initData = $request->input('initData');

        Log::info('TMA: Login attempt started', [
            'has_initData' => !empty($initData),
            'ip' => $request->ip()
        ]);

        if (!$initData) {
            return response()->json(['message' => 'Missing initData'], 400);
        }

        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            return response()->json(['message' => 'Telegram Bot Token not configured'], 500);
        }

        // 1. Validate initData
        $data = $this->validateAndParseInitData($initData, $botToken);

        if (!$data) {
            return response()->json(['message' => 'Invalid Telegram initData signature'], 403);
        }

        // 2. Extract User Info
        $tgUser = json_decode($data['user'], true);
        if (!$tgUser || !isset($tgUser['id'])) {
            return response()->json(['message' => 'Invalid user data in initData'], 400);
        }

        $tgId = (string) $tgUser['id'];

        $isNewRegistration = false;

        // 3. Find or Create Customer
        $customer = $this->customerRepository->findOneByField('telegram_chat_id', $tgId);

        if (!$customer) {
            // Check if we can find by username if they have one
            if (!empty($tgUser['username'])) {
                 $customer = $this->customerRepository->findOneByField('username', $tgUser['username']);
                 
                 // If found, link the Telegram ID
                 if ($customer) {
                     $this->customerRepository->update(['telegram_chat_id' => $tgId], $customer->id);
                 }
            }
        }

        if (!$customer) {
            // Auto-registration
            $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');
            $tgUsername = $tgUser['username'] ?? null;
            
            // 1. Determine unique username
            if ($tgUsername) {
                $username = $tgUsername;
                if ($this->customerRepository->where('username', $username)->exists()) {
                    $username = $tgUsername . '_' . Str::random(4);
                }
            } else {
                $username = 'tg_' . $tgId;
                if ($this->customerRepository->where('username', $username)->exists()) {
                    $username .= '_' . Str::random(4);
                }
            }

            // 2. Generate Wallet (Mnemonic + Address)
            $mnemonicWords = $this->mnemonicService->generateMnemonic(12);
            $recoveryKey = implode(' ', $mnemonicWords);
            $mnemonicHash = $this->mnemonicService->hashMnemonic($mnemonicWords);
            
            $walletData = $this->blockchainAddressService->deriveEthereumWallet($mnemonicWords);
            $blockchainAddress = $walletData['address'];
            $encryptedPrivateKey = \Illuminate\Support\Facades\Crypt::encryptString($walletData['private_key']);

            // 3. Derive Public Key Data
            $publicKeyData = $this->blockchainAddressService->derivePublicKeyData($mnemonicWords);

            // 4. Prepare full customer data
            $currentIp = request()->header('CF-Connecting-IP')
                ?? request()->header('X-Forwarded-For')
                ?? request()->header('X-Real-IP')
                ?? request()->ip();

            if (str_contains($currentIp, ',')) {
                $currentIp = trim(explode(',', $currentIp)[0]);
            }

            $customerData = [
                'first_name'                => $tgUser['first_name'] ?? 'Пользователь',
                'last_name'                 => $tgUser['last_name'] ?? '',
                'email'                     => null, // TMA users might not have email shared
                'username'                  => $username,
                'credits_alias'             => $username,
                'credits_id'                => $blockchainAddress,
                'password'                  => bcrypt($recoveryKey),
                'mnemonic_hash'             => $mnemonicHash,
                'encrypted_private_key'     => $encryptedPrivateKey,
                'public_key'                => $publicKeyData['public_key'] ?? null,
                'public_key_hash'           => $publicKeyData['public_key_hash'] ?? null,
                'telegram_chat_id'          => $tgId,
                'api_token'                 => Str::random(80),
                'token'                     => md5(uniqid(rand(), true)),
                'is_verified'               => 1,
                'customer_group_id'         => app(\Webkul\Customer\Repositories\CustomerGroupRepository::class)->findOneWhere(['code' => $customerGroup])?->getKey(),
                'channel_id'                => core()->getCurrentChannel()?->getKey(),
                'registration_ip'           => $currentIp,
                'last_login_ip'             => $currentIp,
                'status'                    => 1,
                'subscribed_to_news_letter' => 0,
            ];

            try {
                $customer = $this->customerRepository->create($customerData);
                
                // Store in session to show on next page load/auth check
                session(['pending_recovery_key' => $recoveryKey]);
                $isNewRegistration = true;
                
                // Track login activity
                app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);
                
                Log::info('TMA: New user auto-registered with wallet', [
                    'tg_id'      => $tgId, 
                    'username'   => $username, 
                    'address'    => $blockchainAddress
                ]);
            } catch (\Exception $e) {
                Log::error('TMA auto-registration failed: ' . $e->getMessage());
                return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
            }
        }

        // 4. Perform Login
        Auth::guard('customer')->login($customer, true);

        // Track login activity
        app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);

        session()->put('logged_in_via_tma', true);

        Log::info('TMA: Login successful', [
            'customer_id' => $customer->id,
            'username' => $customer->username
        ]);

        return response()->json([
            'success'               => true,
            'is_new_registration'   => $isNewRegistration,
            'redirect_url'          => $isNewRegistration ? route('shop.customers.account.profile.recovery_key') : null,
            'user'    => [
                'id'         => $customer->id,
                'first_name' => $customer->first_name,
                'username'   => $customer->username,
            ]
        ]);
    }

    /**
     * Validate Telegram initData signature.
     *
     * @param  string  $initData
     * @param  string  $botToken
     * @return array|null
     */
    protected function validateAndParseInitData($initData, $botToken)
    {
        parse_str($initData, $data);

        if (!isset($data['hash'])) {
            return null;
        }

        $hash = $data['hash'];
        unset($data['hash']);

        // Sort keys alphabetically
        ksort($data);

        // Construct data check string
        $dataCheckArr = [];
        foreach ($data as $key => $value) {
            $dataCheckArr[] = "{$key}={$value}";
        }
        $dataCheckString = implode("\n", $dataCheckArr);

        // Generate secret key
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);

        // Generate hash
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (hash_equals($calculatedHash, $hash)) {
            return $data;
        }

        return null;
    }
}
