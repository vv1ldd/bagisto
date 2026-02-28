<?php

use Illuminate\Support\Facades\Route;
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\Shop\Http\Controllers\Customer\Account\AddressController;
use Webkul\Shop\Http\Controllers\Customer\Account\DownloadableProductController;
use Webkul\Shop\Http\Controllers\Customer\Account\OrderController;
use Webkul\Shop\Http\Controllers\Customer\Account\WishlistController;
use Webkul\Shop\Http\Controllers\Customer\CustomerController;
use Webkul\Shop\Http\Controllers\Customer\ForgotPasswordController;
use Webkul\Shop\Http\Controllers\Customer\GDPRController;
use Webkul\Shop\Http\Controllers\Customer\RegistrationController;
use Webkul\Shop\Http\Controllers\Customer\ResetPasswordController;
use Webkul\Shop\Http\Controllers\Customer\SessionController;
use Webkul\Shop\Http\Controllers\Customer\VerifyIpController;
use Webkul\Shop\Http\Controllers\DataGridController;
use Webkul\Shop\Http\Controllers\Customer\PasskeyController;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController;

Route::prefix('customer')->group(function () {
    /**
     * Forgot password routes.
     */
    Route::controller(ForgotPasswordController::class)->prefix('forgot-password')->group(function () {
        Route::get('', 'create')->name('shop.customers.forgot_password.create');

        Route::post('', 'store')->name('shop.customers.forgot_password.store');
    });

    /**
     * Reset password routes.
     */
    Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
        Route::get('{token}', 'create')->name('shop.customers.reset_password.create');

        Route::post('', 'store')->name('shop.customers.reset_password.store');
    });

    /**
     * Login routes.
     */
    Route::controller(SessionController::class)->prefix('login')->group(function () {
        Route::get('', 'index')->name('shop.customer.session.index');

        Route::post('', 'store')->name('shop.customer.session.create');

        Route::post('email', 'sendLoginEmail')->name('shop.customer.session.email');

        Route::get('link/{token}', 'loginByLink')->name('shop.customer.login.link');

        Route::get('verify-identity', 'showVerifyIdentity')->name('shop.customer.login.verify_identity');

        Route::post('verify-identity', 'verifyIdentity')->name('shop.customer.login.verify_identity.post');
    });

    /**
     * Passkey routes.
     */
    Route::post('passkeys/login-options', [PasskeyController::class, 'loginOptions'])->name('passkeys.login-options');
    Route::post('passkeys/login', [PasskeyController::class, 'login'])->name('passkeys.login');

    /**
     * Registration routes.
     */
    Route::controller(RegistrationController::class)->group(function () {
        Route::prefix('register')->group(function () {
            Route::get('', 'index')->name('shop.customers.register.index');

            Route::post('', 'store')->name('shop.customers.register.store');
        });

        /**
         * Customer verification routes.
         */
        Route::get('verify-account/{token}', 'verifyAccount')->name('shop.customers.verify');

        Route::get('verify-code', 'showVerifyCode')->name('shop.customers.verify.code');

        Route::post('verify-code', 'verifyByCode')->name('shop.customers.verify.code.submit');

        Route::get('resend/verification/{email}', 'resendVerificationEmail')->name('shop.customers.resend.verification_email');
    });

    /**
     * IP Verification routes.
     */
    Route::controller(VerifyIpController::class)->group(function () {
        Route::get('verify-ip/code', 'showVerifyCode')->name('shop.customers.verify_ip.code');
        Route::post('verify-ip/code', 'verifyByCode')->name('shop.customers.verify_ip.code.submit');
        Route::get('verify-ip/link/{token}', 'verifyByLink')->name('shop.customers.verify_ip.link');
        Route::get('verify-ip/resend', 'resendCode')->name('shop.customers.verify_ip.resend');
    });

    /**
     * Customer authenticated routes. All the below routes only be accessible
     * if customer is authenticated.
     */
    Route::group(['middleware' => ['customer', NoCacheMiddleware::class]], function () {
        /**
         * Datagrid routes.
         */
        Route::get('datagrid/look-up', [DataGridController::class, 'lookUp'])->name('shop.customer.datagrid.look_up');

        /**
         * Passkey authenticated routes.
         */
        Route::controller(PasskeyController::class)->prefix('passkeys')->group(function () {
            Route::get('', 'index')->name('shop.customers.account.passkeys.index');
            Route::post('register-options', 'registerOptions')->name('passkeys.register-options');
            Route::post('register', 'register')->name('passkeys.register');
            Route::delete('{id}', 'destroy')->name('passkeys.destroy');
        });

        /**
         * Login activity routes.
         */
        Route::controller(\Webkul\Shop\Http\Controllers\Customer\Account\LoginHistoryController::class)->prefix('login-activity')->group(function () {
            Route::get('', 'index')->name('shop.customers.account.login_activity.index');
            Route::delete('{id}', 'destroy')->name('shop.customers.account.login_activity.destroy');
        });

        /**
         * Logout.
         */
        Route::get('logout', [SessionController::class, 'destroy'])->name('shop.customer.session.destroy.get');
        Route::delete('logout', [SessionController::class, 'destroy'])->name('shop.customer.session.destroy');

        /**
         * Customer account. All the below routes are related to
         * customer account details.
         */
        Route::prefix('account')->group(function () {
            Route::get('', [CustomerController::class, 'account'])->name('shop.customers.account.index');

            /**
             * Wishlist.
             */
            Route::get('wishlist', [WishlistController::class, 'index'])->name('shop.customers.account.wishlist.index');

            /**
             * Credits (formerly Transactions).
             */
            Route::get('credits', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'index'])->name('shop.customers.account.credits.index');

            /**
             * Profile.
             */
            Route::controller(CustomerController::class)->group(function () {
                Route::prefix('profile')->group(function () {
                    Route::get('edit', 'edit')->name('shop.customers.account.profile.edit');

                    Route::get('recovery-key', 'recoveryKey')->name('shop.customers.account.profile.recovery_key');

                    Route::get('complete-registration', 'completeRegistration')->name('shop.customers.account.profile.complete_registration');

                    Route::get('complete-registration-passkey', 'completeRegistrationPasskey')->name('shop.customers.account.profile.complete_registration_passkey');

                    Route::get('complete-registration-success', 'completeRegistrationSuccess')->name('shop.customers.account.profile.complete_registration_success');


                    Route::post('edit', 'update')->name('shop.customers.account.profile.update');

                    Route::post('destroy', 'destroy')->name('shop.customers.account.profile.destroy');
                });

                Route::get('reviews', 'reviews')->name('shop.customers.account.reviews.index');
            });

            /**
             * GDPR.
             */
            Route::controller(GDPRController::class)->prefix('gdpr')->group(function () {
                Route::get('', 'index')->name('shop.customers.account.gdpr.index');

                Route::post('', 'store')->name('shop.customers.account.gdpr.store');

                Route::get('pdf-view', 'pdfView')->name('shop.customers.account.gdpr.pdf-view');

                Route::get('html-view', 'htmlView')->name('shop.customers.account.gdpr.html-view');

                Route::post('revoke/{id}', 'revoke')->name('shop.customers.account.gdpr.revoke');
            });

            /**
             * Cookie consent.
             */
            Route::get('your-cookie-consent-preferences', [GDPRController::class, 'cookieConsent'])
                ->name('shop.customers.gdpr.cookie-consent');

            /**
             * Addresses.
             */
            Route::controller(AddressController::class)->prefix('addresses')->group(function () {
                Route::get('', 'index')->name('shop.customers.account.addresses.index');

                Route::get('create', 'create')->name('shop.customers.account.addresses.create');

                Route::post('create', 'store')->name('shop.customers.account.addresses.store');

                Route::get('edit/{id}', 'edit')->name('shop.customers.account.addresses.edit');

                Route::put('edit/{id}', 'update')->name('shop.customers.account.addresses.update');

                Route::patch('edit/{id}', 'makeDefault')->name('shop.customers.account.addresses.update.default');

                Route::delete('delete/{id}', 'destroy')->name('shop.customers.account.addresses.delete');
            });

            /**
             * Orders.
             */
            Route::controller(OrderController::class)->prefix('orders')->group(function () {
                Route::get('', 'index')->name('shop.customers.account.orders.index');

                Route::get('view/{id}', 'view')->name('shop.customers.account.orders.view');

                Route::get('reorder/{id}', 'reorder')->name('shop.customers.account.orders.reorder');

                Route::post('cancel/{id}', 'cancel')->name('shop.customers.account.orders.cancel');

                Route::get('print/Invoice/{id}', 'printInvoice')->name('shop.customers.account.orders.print-invoice');
            });

            /**
             * Downloadable products.
             */
            Route::controller(DownloadableProductController::class)->prefix('downloadable-products')->group(function () {
                Route::get('', 'index')->name('shop.customers.account.downloadable_products.index');

                Route::get('download/{id}', 'download')->name('shop.customers.account.downloadable_products.download');
            });
        });
    });
});
