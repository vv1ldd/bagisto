<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;

/**
 * Auth routes.
 */
require 'auth-routes.php';

Route::group(['domain' => config('app.admin_domain')], function () {
    Route::group([
        'prefix'     => config('app.admin_url'),
        'middleware' => ['admin', NoCacheMiddleware::class],
    ], function () {
        /**
         * Configuration routes.
         */
        require 'configuration-routes.php';

        /**
         * Sales routes.
         */
        require 'sales-routes.php';

        /**
         * Catalog routes.
         */
        require 'catalog-routes.php';

        /**
         * Customers routes.
         */
        require 'customers-routes.php';

        /**
         * Marketing routes.
         */
        require 'marketing-routes.php';

        /**
         * CMS routes.
         */
        require 'cms-routes.php';

        /**
         * Reporting routes.
         */
        require 'reporting-routes.php';

        /**
         * Settings routes.
         */
        require 'settings-routes.php';

        /**
         * Notification routes.
         */
        require 'notification-routes.php';

        /**
         * MagicAI routes.
         */
        require 'magic-ai-routes.php';

        /**
         * Remaining routes.
         */
        require 'rest-routes.php';
    });
});
