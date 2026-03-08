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

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'magic_ai');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        include __DIR__ . '/../Http/helpers.php';

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/magic_ai.php',
            'magic_ai_settings'
        );
    }
}
