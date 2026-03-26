<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Repositories\CustomerRepository;

class TelegramService
{
    /**
     * @var string|null
     */
    protected $token;

    /**
     * Create a new service instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository
    ) {
        $this->token = config('services.telegram.bot_token');
    }

    /**
     * Send a message to a specific chat ID.
     *
     * @param  int  $chatId
     * @param  string  $message
     * @param  string  $parseMode
     * @return bool
     */
    public function sendMessage(int $chatId, string $message, string $parseMode = 'HTML'): bool
    {
        if (!$this->token) {
            Log::error('Telegram Bot Token not configured.');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => $parseMode,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram sendMessage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Link a customer to a Telegram chat ID using a token.
     *
     * @param  string  $token
     * @param  int  $chatId
     * @return \Webkul\Customer\Models\Customer|null
     */
    public function linkCustomer(string $token, int $chatId)
    {
        $customer = $this->customerRepository->findOneByField('telegram_token', $token);

        if (!$customer) {
            return null;
        }

        $this->customerRepository->update([
            'telegram_chat_id' => $chatId,
            'telegram_token'   => null,
        ], $customer->id);

        return $customer->fresh();
    }
}
