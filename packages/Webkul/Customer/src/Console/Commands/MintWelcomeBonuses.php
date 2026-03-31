<?php

namespace Webkul\Customer\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerTransaction;
use App\Jobs\ProcessWelcomeMintingJob;

class MintWelcomeBonuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:mint-welcome-bonuses {--dry-run : Only show what would be minted without actual processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mints welcome bonuses for customers who have not received them yet on the blockchain.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Starting Welcome Bonus Minting Check...');

        // 1. Get customers who are verified and have a crypto address (credits_id) 
        // 2. Filter out those who already have a registration_minting or welcome_bonus transaction
        $customers = Customer::where('is_verified', 1)
            ->whereNotNull('credits_id')
            ->where('credits_id', 'like', '0x%') // Ensure it is an EVM address
            ->get();

        $count = 0;
        foreach ($customers as $customer) {
            $hasBonus = CustomerTransaction::where('customer_id', $customer->id)
                ->whereIn('type', ['registration_minting', 'welcome_bonus'])
                ->exists();

            if ($hasBonus) {
                continue;
            }

            $this->comment("Target: Customer [ID: {$customer->id}] {$customer->email} - Address: {$customer->credits_id}");

            if (!$this->option('dry-run')) {
                ProcessWelcomeMintingJob::dispatch($customer->id);
                $this->info(" -> Dispatched ProcessWelcomeMintingJob");
            }

            $count++;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry run complete. {$count} customers would have received the bonus.");
        } else {
            $this->info("Finished. {$count} minting jobs dispatched to the queue.");
        }
    }
}
