<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerTransaction;

class InternalTransferService
{
    /**
     * Transfer credits from sender to recipient.
     *
     * @param  Customer  $sender
     * @param  Customer  $recipient
     * @param  float|decimal  $amount
     * @param  string|null  $notes
     * @return bool
     * @throws \Exception
     */
    public function transfer(Customer $sender, Customer $recipient, $amount, $notes = null): bool
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be positive.');
        }

        if ($sender->id === $recipient->id) {
            throw new \Exception('Cannot transfer to yourself.');
        }

        if ($sender->balance < $amount) {
            throw new \Exception('Insufficient balance.');
        }

        return DB::transaction(function () use ($sender, $recipient, $amount, $notes) {
            // Deduct from sender
            $sender->decrement('balance', $amount);

            // Add to recipient
            $recipient->increment('balance', $amount);

            $transferUuid = (string) Str::uuid();

            // Create DEBIT transaction for sender
            CustomerTransaction::create([
                'uuid' => (string) Str::uuid(),
                'customer_id' => $sender->id,
                'amount' => -$amount,
                'type' => 'transfer_debit',
                'status' => 'completed',
                'reference_type' => Customer::class,
                'reference_id' => $recipient->id,
                'notes' => "Перевод пользователю @{$recipient->credits_alias}: " . ($notes ?? ''),
                'metadata' => [
                    'transfer_uuid' => $transferUuid,
                    'recipient_id' => $recipient->id,
                    'recipient_alias' => $recipient->credits_alias,
                ],
            ]);

            // Create CREDIT transaction for recipient
            CustomerTransaction::create([
                'uuid' => (string) Str::uuid(),
                'customer_id' => $recipient->id,
                'amount' => $amount,
                'type' => 'transfer_credit',
                'status' => 'completed',
                'reference_type' => Customer::class,
                'reference_id' => $sender->id,
                'notes' => "Получен перевод от @{$sender->credits_alias}: " . ($notes ?? ''),
                'metadata' => [
                    'transfer_uuid' => $transferUuid,
                    'sender_id' => $sender->id,
                    'sender_alias' => $sender->credits_alias,
                ],
            ]);

            return true;
        });
    }
}
