<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployContract extends Command
{
    protected $signature = 'crypto:deploy-contract';

    protected $description = 'Install Foundry (if needed) and deploy the MeanlyGifts ERC-1155 contract to the blockchain.';

    public function handle(): int
    {
        $rpcUrl     = env('ALCHEMY_RPC_URL');
        $privateKey = env('ADMIN_ETH_PRIVATE_KEY');
        $ownerAddr  = env('ADMIN_ETH_PUBLIC_ADDRESS', '0xB1ABfEab7E90B8565F715871f8a0fF1B9FD9F9AA');
        $contractPath = base_path('contracts/MeanlyGifts.sol');

        // ---------------------------------------------------------------
        // 1. Guard checks
        // ---------------------------------------------------------------
        if (!$rpcUrl) {
            $this->error('ALCHEMY_RPC_URL is not set in .env');
            return 1;
        }
        if (!$privateKey) {
            $this->error('ADMIN_ETH_PRIVATE_KEY is not set in .env');
            return 1;
        }
        if (!file_exists($contractPath)) {
            $this->error("Contract file not found at: {$contractPath}");
            return 1;
        }

        // ---------------------------------------------------------------
        // 2. Locate forge (check common paths before installing)
        // ---------------------------------------------------------------
        $home        = rtrim(getenv('HOME') ?: '/root', '/');
        $forgeDirect  = "{$home}/.foundry/bin/forge";
        $forgeInPath  = trim(shell_exec('which forge 2>/dev/null'));

        if (is_executable($forgeDirect)) {
            $forge = $forgeDirect;
            $this->info("Found forge at: {$forge}");
        } elseif (!empty($forgeInPath) && is_executable($forgeInPath)) {
            $forge = $forgeInPath;
            $this->info("Found forge at: {$forge}");
        } else {
            $this->warn('Foundry (forge) not found. Installing...');

            passthru('curl -L https://foundry.paradigm.xyz | bash', $r1);
            if ($r1 !== 0) {
                $this->error('Failed to download Foundry installer.');
                return 1;
            }

            passthru("{$home}/.foundry/bin/foundryup", $r2);
            if ($r2 !== 0) {
                $this->error('foundryup failed. Please run `foundryup` manually.');
                return 1;
            }

            $forge = $forgeDirect;
            $this->info('Foundry installed successfully!');
        }

        $this->line("Using forge: {$forge}");
        $this->line('');

        // ---------------------------------------------------------------
        // 3. Check ETH balance — required to broadcast
        // ---------------------------------------------------------------
        $this->info("Checking ETH balance on {$ownerAddr}...");
        $balanceJson = @file_get_contents($rpcUrl, false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => json_encode([
                    'jsonrpc' => '2.0',
                    'method'  => 'eth_getBalance',
                    'params'  => [$ownerAddr, 'latest'],
                    'id'      => 1,
                ]),
            ],
        ]));

        if ($balanceJson) {
            $balanceData = json_decode($balanceJson, true);
            $weiHex = $balanceData['result'] ?? '0x0';
            $wei    = hexdec(ltrim($weiHex, '0x') ?: '0');
            $eth    = $wei / 1e18;
            $this->line("  Balance: {$eth} ETH");

            if ($eth < 0.0001) {
                $this->error("❌ Insufficient balance! The hot wallet needs ETH to pay for gas.");
                $this->line("   Send at least $1–2 worth of ETH (Arbitrum One network) to:");
                $this->line("   {$ownerAddr}");
                $this->line('');
                $this->line("   Then re-run: php artisan crypto:deploy-contract");
                return 1;
            }
        }

        $this->line('');

        // ---------------------------------------------------------------
        // 4. Write a temporary shell script so we don't have to worry
        //    about quoting/escaping – every argument goes on its own line.
        // ---------------------------------------------------------------
        $tmpScript = sys_get_temp_dir() . '/meanly_deploy_' . time() . '.sh';

        $scriptContent = "#!/usr/bin/env bash\n"
                       . "export PRIVATE_KEY=\"{$privateKey}\"\n"
                       . "\"{$forge}\" create \"{$contractPath}:MeanlyGifts\" --rpc-url \"{$rpcUrl}\" --private-key \"\$PRIVATE_KEY\" --broadcast --legacy --constructor-args \"{$ownerAddr}\"\n";

        file_put_contents($tmpScript, $scriptContent);
        chmod($tmpScript, 0700);

        $this->info('Compiling and deploying MeanlyGifts.sol to Arbitrum...');
        $this->line('(this may take 30–60 seconds)');
        $this->line('');

        // ---------------------------------------------------------------
        // 5. Run the temporary script
        // ---------------------------------------------------------------
        $output   = [];
        $exitCode = 0;
        exec("{$tmpScript} 2>&1", $output, $exitCode);

        // Clean up temp script immediately after execution
        @unlink($tmpScript);

        $fullOutput = implode("\n", $output);
        $this->line($fullOutput);

        if ($exitCode !== 0) {
            $this->error('Deployment failed! Check the output above.');
            return 1;
        }

        // ---------------------------------------------------------------
        // 6. Parse out the deployed contract address
        // ---------------------------------------------------------------
        $contractAddress = null;
        foreach ($output as $line) {
            if (preg_match('/Deployed to:\s*(0x[0-9a-fA-F]{40})/i', $line, $matches)) {
                $contractAddress = $matches[1];
                break;
            }
        }

        if (!$contractAddress) {
            $this->warn('Deployment seemed to succeed but could not extract contract address automatically.');
            $this->warn('Look for "Deployed to: 0x..." in the output above and copy it to your .env as MINT_CONTRACT_ADDRESS=');
            return 0;
        }

        // ---------------------------------------------------------------
        // 7. Inform the user
        // ---------------------------------------------------------------
        $this->line('');
        $this->info('✅ Contract deployed successfully!');
        $this->line('');
        $this->line("  <fg=green>Contract Address: {$contractAddress}</>");
        $this->line('');
        $this->warn('Add this to your .env file on the server and run: php artisan config:clear');
        $this->line("  MINT_CONTRACT_ADDRESS={$contractAddress}");
        $this->line('');

        // Optionally store it in the .env file automatically
        if ($this->confirm('Would you like me to automatically update your .env file?', true)) {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            if (str_contains($envContent, 'MINT_CONTRACT_ADDRESS=')) {
                $envContent = preg_replace('/^MINT_CONTRACT_ADDRESS=.*/m', "MINT_CONTRACT_ADDRESS={$contractAddress}", $envContent);
            } else {
                $envContent .= "\nMINT_CONTRACT_ADDRESS={$contractAddress}\n";
            }

            file_put_contents($envPath, $envContent);
            $this->info(".env updated! Run: php artisan config:clear");
        }

        return 0;
    }
}
