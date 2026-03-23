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
        // 2. Install Foundry if forge is not found
        // ---------------------------------------------------------------
        $forgeExists = trim(shell_exec('which forge 2>/dev/null'));

        if (empty($forgeExists)) {
            $this->warn('Foundry (forge) not found. Installing...');

            // Download and run the Foundry installer non-interactively
            passthru('curl -L https://foundry.paradigm.xyz | bash', $installResult);

            if ($installResult !== 0) {
                $this->error('Failed to install Foundry. Please install it manually: https://getfoundry.sh');
                return 1;
            }

            // Source the profile and run foundryup
            passthru('$HOME/.foundry/bin/foundryup', $foundryUpResult);

            if ($foundryUpResult !== 0) {
                $this->error('foundryup failed. Please run `foundryup` manually and retry.');
                return 1;
            }

            $forgeExists = trim(shell_exec('which $HOME/.foundry/bin/forge 2>/dev/null')) 
                        ?: ($HOME = getenv('HOME')) . '/.foundry/bin/forge';

            $this->info('Foundry installed successfully!');
        }

        $forge = !empty($forgeExists) ? $forgeExists : (getenv('HOME') . '/.foundry/bin/forge');

        // ---------------------------------------------------------------
        // 3. Check if forge binary is executable
        // ---------------------------------------------------------------
        if (!is_executable($forge)) {
            $forge = getenv('HOME') . '/.foundry/bin/forge';
        }

        $this->info("Using forge at: {$forge}");
        $this->line('');

        // ---------------------------------------------------------------
        // 4. Write a temporary shell script so we don't have to worry
        //    about quoting/escaping – every argument goes on its own line.
        // ---------------------------------------------------------------
        $tmpScript = sys_get_temp_dir() . '/meanly_deploy_' . time() . '.sh';

        $scriptContent = <<<BASH
#!/usr/bin/env bash
export PRIVATE_KEY="{$privateKey}"
"{$forge}" create "{$contractPath}:MeanlyGifts" \\
    --rpc-url "{$rpcUrl}" \\
    --private-key "\$PRIVATE_KEY" \\
    --constructor-args "{$ownerAddr}" \\
    --broadcast
BASH;

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
