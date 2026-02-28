<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\RecipientLookupService;
use Webkul\Customer\Services\InternalTransferService;

class TransferController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  RecipientLookupService  $recipientLookupService
     * @param  InternalTransferService  $internalTransferService
     * @return void
     */
    public function __construct(
        protected RecipientLookupService $recipientLookupService,
        protected InternalTransferService $internalTransferService
    ) {
    }

    /**
     * Handle the transfer request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:0.0001',
            'notes' => 'nullable|string|max:255',
        ]);

        $sender = auth()->guard('customer')->user();

        $recipient = $this->recipientLookupService->find($request->recipient);

        if (!$recipient) {
            return redirect()->back()
                ->with('error', 'Получатель не найден. Проверьте алиас или ID.')
                ->withInput();
        }

        if ($sender->id === $recipient->id) {
            return redirect()->back()
                ->with('error', 'Вы не можете отправить перевод самому себе.')
                ->withInput();
        }

        try {
            $this->internalTransferService->transfer(
                $sender,
                $recipient,
                $request->amount,
                $request->notes
            );

            session()->flash('success', "Перевод пользователю @{$recipient->credits_alias} успешно выполнен.");

            return redirect()->route('shop.customers.account.credits.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
