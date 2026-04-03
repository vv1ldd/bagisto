<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessWelcomeMintingJob;

class WelcomeBonusListener
{
    /**
     * Handle the event.
     */
    public function handle($customer): void
    {
        if (!$customer || !$customer->id) {
            return;
        }

        // Dispatch if a wallet exists (mnemonic_hash is set), regardless of verification status
        if (!empty($customer->mnemonic_hash)) {
            Log::info("WelcomeBonusListener: Dispatching Welcome Bonus for Customer [{$customer->id}]");
            ProcessWelcomeMintingJob::dispatch($customer->id);
        }
    }
}
