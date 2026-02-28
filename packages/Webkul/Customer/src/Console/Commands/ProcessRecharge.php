<?php

namespace Webkul\Customer\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Customer\Models\CryptoAddress;
use Webkul\Customer\Services\BlockchainSyncService;

class ProcessRecharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:process-recharge {--address= : Specify an address to scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan verified crypto addresses for new deposits and top up balances.';

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

        // Only scan verified addresses
        $query = CryptoAddress::whereNotNull('verified_at');

        if ($address) {
            $query->where('address', $address);
        }

        $addresses = $query->get();

        if ($addresses->isEmpty()) {
            $this->info('No verified crypto addresses found to scan.');
            return 0;
        }

        $this->info("Scanning for deposits for {$addresses->count()} addresses...");
        $this->output->progressStart($addresses->count());

        $totalDeposits = 0;

        foreach ($addresses as $cryptoAddress) {
            $newTxs = $this->syncService->syncDeposits($cryptoAddress);
            $totalDeposits += count($newTxs);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info("Scan completed. Processed {$totalDeposits} new deposits.");

        return 0;
    }
}
