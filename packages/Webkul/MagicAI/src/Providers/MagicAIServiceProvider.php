<?php

namespace Webkul\MagicAI\Providers;

use Illuminate\Support\ServiceProvider;

class MagicAIServiceProvider extends ServiceProvider
{
    /**
     * Boot services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        include __DIR__ . '/../Http/helpers.php';

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core_config'
        );
    }
}
