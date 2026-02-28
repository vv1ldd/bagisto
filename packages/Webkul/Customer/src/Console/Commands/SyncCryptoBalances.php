<?php

namespace Webkul\Customer\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Customer\Models\CryptoAddress;
use Webkul\Customer\Services\BlockchainSyncService;

class SyncCryptoBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:sync-balances {--address= : Specify an address to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize crypto address balances from blockchain explorers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected BlockchainSyncService $syncService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $address = $this->option('address');

        $query = CryptoAddress::where('is_active', true);

        if ($address) {
            $query->where('address', $address);
        }

        $addresses = $query->get();

        if ($addresses->isEmpty()) {
            $this->info('No active crypto addresses found to sync.');
            return 0;
        }

        $this->info("Starting balance sync for {$addresses->count()} addresses...");
        $this->output->progressStart($addresses->count());

        foreach ($addresses as $cryptoAddress) {
            $this->syncService->syncBalance($cryptoAddress);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Balance synchronization completed successfully.');

        return 0;
    }
}
