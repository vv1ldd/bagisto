<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\TelegramService;

class TelegramBotController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Services\TelegramService  $telegramService
     * @return void
     */
    public function __construct(
        protected TelegramService $telegramService
    ) {
    }

    /**
     * Handle the incoming Telegram Webhook.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        if (isset($payload['message'])) {
            $message = $payload['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';

            // Handle /start <token> command
            if (str_starts_with($text, '/start')) {
                $args = explode(' ', $text);
                $token = isset($args[1]) ? trim($args[1]) : null;

                if ($token) {
                    $customer = $this->telegramService->linkCustomer($token, $chatId);

                    if ($customer) {
                        $this->telegramService->sendMessage(
                            $chatId,
                            "<b>Аккаунт успешно привязан!</b>\n\nТеперь вы будете получать уведомления от Meanly в этот чат."
                        );
                        
                        // Also show the app button after linking
                        $this->sendWelcomeWithAppButton($chatId);
                    } else {
                        $this->telegramService->sendMessage(
                            $chatId,
                            "Ошибка: Неверный или просроченный токен. Пожалуйста, получите новую ссылку в личном кабинете."
                        );
                    }
                } else {
                    // Standard /start without token
                    $this->sendWelcomeWithAppButton($chatId);
                }
            } else {
                // Any other message - show the button for convenience
                $this->sendWelcomeWithAppButton($chatId);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Send a welcome message with a button to open the Mini App.
     *
     * @param  int  $chatId
     * @return void
     */
    protected function sendWelcomeWithAppButton(int $chatId)
    {
        $appUrl = url('/');
        
        $this->telegramService->sendMessage(
            $chatId,
            "<b>Добро пожаловать в Meanly Market!</b>\n\nСамый современный маркетплейс уже здесь. Нажмите кнопку ниже, чтобы войти в приложение.",
            'HTML',
            [
                'inline_keyboard' => [
                    [
                        [
                            'text'    => '🚀 Открыть Meanly Market',
                            'web_app' => ['url' => $appUrl]
                        ]
                    ]
                ]
            ]
        );
    }
}
