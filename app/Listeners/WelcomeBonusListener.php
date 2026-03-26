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

        // Only dispatch for verified customers (e.g. Passkey registration)
        // For standard email reg, this will be called again or handled in verification controllers
        if ($customer->is_verified) {
            Log::info("WelcomeBonusListener: Dispatching Welcome Bonus for Customer [{$customer->id}]");
            ProcessWelcomeMintingJob::dispatch($customer->id);
        }
    }
}
