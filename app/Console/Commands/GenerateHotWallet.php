<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateHotWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:generate-hot-wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a secure Ethereum Hot Wallet for the platform explicitly for sending NFTs and paying Gas.';

    /**
     * Execute the console command.
     */
    public function handle(\Webkul\Customer\Services\MnemonicService $mnemonicService, \Webkul\Customer\Services\BlockchainAddressService $addressService)
    {
        $this->info('Generating a new, secure Ethereum Hot Wallet...');

        $words = $mnemonicService->generateMnemonic(12);
        
        $this->line('');
        $this->warn('--- VERY IMPORTANT: SAVE THIS RECOVERY PHRASE OFFLINE ---');
        $this->error(implode(' ', $words));
        $this->warn('--------------------------------------------------------');
        $this->line('');

        $wallet = $addressService->deriveEthereumWallet($words);

        $this->info('Wallet successfully generated!');
        $this->line('');
        $this->info('Public Address: <fg=green>' . $wallet['address'] . '</>');
        $this->line('');
        
        $this->warn('Add this Private Key to your .env file as:');
        $this->error('ADMIN_ETH_PRIVATE_KEY=' . $wallet['private_key']);
        $this->line('');
        
        $this->info('Make sure to send some Arbitrum Sepolia ETH / ETH to the Public Address to pay for gas!');
    }
}
