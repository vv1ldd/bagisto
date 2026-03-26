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
            if (str_starts_with($text, '/start ')) {
                $token = trim(substr($text, 7));

                if ($token) {
                    $customer = $this->telegramService->linkCustomer($token, $chatId);

                    if ($customer) {
                        $this->telegramService->sendMessage(
                            $chatId,
                            "<b>Аккаунт успешно привязан!</b>\n\nТеперь вы будете получать уведомления от Meanly в этот чат."
                        );
                    } else {
                        $this->telegramService->sendMessage(
                            $chatId,
                            "Ошибка: Неверный или просроченный токен. Пожалуйста, получите новую ссылку в личном кабинете."
                        );
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
