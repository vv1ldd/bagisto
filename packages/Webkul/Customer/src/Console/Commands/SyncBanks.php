<?php

namespace Webkul\Customer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Webkul\Customer\Repositories\BankRepository;

class SyncBanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banks:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync banks and BICs from CBR (Central Bank of Russia)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(protected BankRepository $bankRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Starting banks synchronization from CBR...');

        try {
            // CBR BIC XML source
            $url = 'http://www.cbr.ru/scripts/XML_bic2.asp';
            $response = Http::timeout(60)->get($url);

            if (!$response->successful()) {
                $this->error('Failed to fetch data from CBR. Status: ' . $response->status());
                return 1;
            }

            $xml = simplexml_load_string($response->body());
            if (!$xml) {
                $this->error('Failed to parse XML from CBR.');
                return 1;
            }

            $count = 0;
            $updated = 0;

            foreach ($xml->Record as $record) {
                $bic = (string) $record['BIC'];
                $name = (string) $record->ShortName;
                if (!$name)
                    $name = (string) $record->Name;

                $corrAccount = (string) $record->CorrAddr;
                $address = (string) $record->Address;

                if (!$bic || !$name)
                    continue;

                $this->bankRepository->updateOrCreate([
                    'bic' => $bic,
                ], [
                    'name' => $name,
                    'correspondent_account' => $corrAccount,
                    'address' => $address,
                ]);

                $count++;
                if ($count % 50 === 0) {
                    $this->line("Processed {$count} banks...");
                }
            }

            $this->info("Successfully synchronized {$count} banks.");

        } catch (\Exception $e) {
            $this->error('Error during synchronization: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
