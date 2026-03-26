<?php

namespace Webkul\Customer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UpdateHydrogenChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:update-hydrogen {--version=latest : Specific version to download}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and update Hydrogen Matrix client assets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->option('version');
        $this->info("Fetching Hydrogen assets ({$version})...");

        try {
            if ($version === 'latest') {
                $response = Http::get('https://api.github.com/repos/element-hq/hydrogen-web/releases/latest');
                
                if (!$response->successful()) {
                    $this->error("Failed to fetch latest release from GitHub.");
                    return 1;
                }

                $data = $response->json();
                $version = $data['tag_name'];
                
                // Find the web bundle asset
                $asset = collect($data['assets'])->first(function ($asset) {
                    return str_contains($asset['name'], 'hydrogen-web') && str_ends_with($asset['name'], '.tar.gz');
                });

                if (!$asset) {
                    $this->error("Could not find web bundle asset in release {$version}.");
                    return 1;
                }

                $downloadUrl = $asset['browser_download_url'];
            } else {
                // Construct URL for specific version (simplified)
                $v = ltrim($version, 'v');
                $downloadUrl = "https://github.com/element-hq/hydrogen-web/releases/download/{$version}/hydrogen-web-{$v}.tar.gz";
            }

            $this->info("Downloading from: {$downloadUrl}");

            $tempPath = storage_path('hydrogen.tar.gz');
            $response = Http::get($downloadUrl);
            
            if (!$response->successful()) {
                $this->error("Failed to download Hydrogen assets.");
                return 1;
            }

            File::put($tempPath, $response->body());

            $publicPath = public_path('chat');
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }

            $this->info("Extracting to {$publicPath}...");
            
            // Using shell command for extraction to handle --strip-components
            $command = "tar -xzf " . escapeshellarg($tempPath) . " -C " . escapeshellarg($publicPath) . " --strip-components=1";
            exec($command, $output, $resultCode);

            if ($resultCode !== 0) {
                $this->error("Extraction failed.");
                return 1;
            }

            File::delete($tempPath);

            $this->info("Successfully updated Hydrogen to {$version}!");
            return 0;

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            return 1;
        }
    }
}
