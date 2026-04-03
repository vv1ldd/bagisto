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

        // Dispatch for verified customers or those with a generated wallet (mnemonic_hash exists)
        if ($customer->is_verified || !empty($customer->mnemonic_hash)) {
            Log::info("WelcomeBonusListener: Dispatching Welcome Bonus for Customer [{$customer->id}]");
            ProcessWelcomeMintingJob::dispatch($customer->id);
        }
    }
}
