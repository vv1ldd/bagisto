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
        protected CustomerRepository $customerRepository
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
            $username = $tgUser['username'] ?? 'tg_' . $tgId;
            
            // Generate a random unique password (though they login via TG)
            $password = bcrypt(Str::random(32));

            $data = [
                'first_name'                => $tgUser['first_name'] ?? 'Пользователь',
                'last_name'                 => $tgUser['last_name'] ?? '',
                'email'                     => null, // TMA users might not have email shared
                'username'                  => $username,
                'credits_alias'             => $username,
                'password'                  => $password,
                'telegram_chat_id'          => $tgId,
                'is_verified'               => 1,
                'customer_group_id'         => app(\Webkul\Customer\Repositories\CustomerGroupRepository::class)->findOneWhere(['code' => $customerGroup])->id,
                'channel_id'                => core()->getCurrentChannel()?->id,
                'status'                    => 1,
                'subscribed_to_news_letter' => 0,
            ];

            try {
                $customer = $this->customerRepository->create($data);
                
                // Track login activity
                app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);
                
                Log::info('TMA: New user registered', ['tg_id' => $tgId, 'customer_id' => $customer->id]);
            } catch (\Exception $e) {
                Log::error('TMA registration failed: ' . $e->getMessage());
                return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
            }
        }

        // 4. Perform Login
        Auth::guard('customer')->login($customer, true);

        // Track login activity
        app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);

        session()->put('logged_in_via_tma', true);

        return response()->json([
            'success' => true,
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
