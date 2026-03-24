<?php

use App\Http\Middleware\EncryptCookies;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Webkul\Core\Http\Middleware\SecureHeaders;
use Webkul\Installer\Http\Middleware\CanInstall;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['middleware' => ['web']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        /**
         * Remove the default Laravel middleware that prevents requests during maintenance mode. There are three
         * middlewares in the shop that need to be loaded before this middleware. Therefore, we need to remove this
         * middleware from the list and add the overridden middleware at the end of the list.
         *
         * As of now, this has been added in the Admin and Shop providers. I will look for a better approach in Laravel 11 for this.
         */
        $middleware->remove(PreventRequestsDuringMaintenance::class);

        /**
         * Remove the default Laravel middleware that converts empty strings to null. First, handle all nullable cases,
         * then remove this line.
         */
        $middleware->remove(ConvertEmptyStringsToNull::class);

        $middleware->append(SecureHeaders::class);
        $middleware->append(CanInstall::class);

        /**
         * Add the overridden middleware at the end of the list.
         */
        $middleware->replaceInGroup('web', BaseEncryptCookies::class, EncryptCookies::class);

        $middleware->trustProxies('*');
    })
    ->withSchedule(function (Schedule $schedule) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
            'mnemonic',
        ]);

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, \Illuminate\Http\Request $request) {
                $statusCode = $e->getStatusCode();
                
                if (in_array($statusCode, [401, 403, 404, 500, 503])) {
                    $isAdmin = $request->is(config('app.admin_url').'/*') || $request->getHost() === config('app.admin_domain', 'timebeing.meanly.ru');
                    $namespace = $isAdmin ? 'admin' : 'shop';

                    if ($request->wantsJson() || $request->is('api/*')) {
                        return response()->json([
                            'error' => trans("{$namespace}::app.errors.{$statusCode}.title"),
                            'description' => trans("{$namespace}::app.errors.{$statusCode}.description"),
                        ], $statusCode);
                    }

                    $viewPath = "{$namespace}::errors.{$statusCode}";
                    if (! view()->exists($viewPath)) {
                        $viewPath = "{$namespace}::errors.index";
                    }

                    if (view()->exists($viewPath)) {
                        return response()->view($viewPath, ['errorCode' => $statusCode], $statusCode);
                    }
                }
            });
    })->create();
