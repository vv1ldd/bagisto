<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\User\ForgetPasswordController;
use Webkul\Admin\Http\Controllers\User\ResetPasswordController;
use Webkul\Admin\Http\Controllers\User\SessionController;

/**
 * Auth routes.
 */
Route::group([
    'domain'     => config('app.admin_domain'),
    'prefix'     => config('app.admin_url'),
    'middleware' => ['throttle:10,1'],
], function () {
    /**
     * Redirect route.
     */
    Route::get('/', [Controller::class, 'redirectToLogin']);

    Route::controller(SessionController::class)->prefix('login')->group(function () {
        /**
         * Login routes.
         */
        Route::get('', 'create')->name('admin.session.create');

        /**
         * Login post route to admin auth controller.
         */
        Route::post('', 'store')->name('admin.session.store');

        /**
         * Recovery routes.
         */
        Route::get('recovery', 'showRecovery')->name('admin.session.recovery.create');

        Route::post('recovery', 'recover')->name('admin.session.recovery.store');
    });

    /**
     * Passkey login routes.
     */
    Route::controller(PasskeyController::class)->prefix('passkey')->group(function () {
        Route::post('login-options', 'loginOptions')->name('admin.passkey.login_options');

        Route::post('login', 'login')->name('admin.passkey.login');
    });

    /**
     * Forget password routes.
     */
    Route::controller(ForgetPasswordController::class)->prefix('forget-password')->group(function () {
        Route::get('', 'create')->name('admin.forget_password.create');

        Route::post('', 'store')->name('admin.forget_password.store');
    });

    /**
     * Reset password routes.
     */
    Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
        Route::get('{token}', 'create')->name('admin.reset_password.create');

        Route::post('', 'store')->name('admin.reset_password.store');
    });
});
