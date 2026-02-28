<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Customer\Models\CustomerTransactionProxy;
use Webkul\Customer\Repositories\CustomerTransactionRepository;
use Exception;

class LedgerService
{
    /**
     * Create a new service instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerTransactionRepository  $customerTransactionRepository
     * @return void
     */
    public function __construct(
        protected CustomerTransactionRepository $customerTransactionRepository
    ) {
    }

    /**
     * Credit an amount to a customer's balance.
     *
     * @param  \Webkul\Customer\Models\Customer  $customer
     * @param  float  $amount
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Model|null  $reference
     * @param  string|null  $notes
     * @param  string|null  $uuid
     * @return \Webkul\Customer\Models\CustomerTransaction
     * @throws \Exception
     */
    public function credit($customer, $amount, $type, $reference = null, $notes = null, $uuid = null)
    {
        $uuid = $uuid ?: (string) Str::uuid();

        // Idempotency check
        $existing = $this->customerTransactionRepository->findOneByField('uuid', $uuid);
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($customer, $amount, $type, $reference, $notes, $uuid) {
            // Lock the customer row for update to prevent race conditions
            $customer = $customer->newQuery()->lockForUpdate()->find($customer->id);

            $transaction = $this->customerTransactionRepository->create([
                'uuid' => $uuid,
                'customer_id' => $customer->id,
                'amount' => abs($amount),
                'type' => $type,
                'status' => 'completed',
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'notes' => $notes,
            ]);

            $customer->balance += abs($amount);
            $customer->save();

            return $transaction;
        });
    }

    /**
     * Debit an amount from a customer's balance.
     *
     * @param  \Webkul\Customer\Models\Customer  $customer
     * @param  float  $amount
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Model|null  $reference
     * @param  string|null  $notes
     * @param  string|null  $uuid
     * @return \Webkul\Customer\Models\CustomerTransaction
     * @throws \Exception
     */
    public function debit($customer, $amount, $type, $reference = null, $notes = null, $uuid = null)
    {
        $uuid = $uuid ?: (string) Str::uuid();

        // Idempotency check
        $existing = $this->customerTransactionRepository->findOneByField('uuid', $uuid);
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($customer, $amount, $type, $reference, $notes, $uuid) {
            // Lock the customer row for update
            $customer = $customer->newQuery()->lockForUpdate()->find($customer->id);

            if ($customer->balance < abs($amount)) {
                throw new Exception('Insufficient balance.');
            }

            $transaction = $this->customerTransactionRepository->create([
                'uuid' => $uuid,
                'customer_id' => $customer->id,
                'amount' => -abs($amount),
                'type' => $type,
                'status' => 'completed',
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'notes' => $notes,
            ]);

            $customer->balance -= abs($amount);
            $customer->save();

            return $transaction;
        });
    }
}
