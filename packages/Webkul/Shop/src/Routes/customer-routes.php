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

use Webkul\Shop\Http\Controllers\DataGridController;
use Webkul\Shop\Http\Controllers\Customer\PasskeyController;
use Webkul\Shop\Http\Controllers\Customer\Account\TransferController;
use Webkul\Shop\Http\Controllers\Customer\Account\RedeemController;
use Webkul\Shop\Http\Controllers\Customer\QrLoginController;

Route::group([], function () {
    Route::get('/test-mail', function () {
        try {
            \Illuminate\Support\Facades\Mail::raw('This is a test email from Bagisto to verify mail configuration.', function ($message) {
                $message->to(request('email', 'admin@example.com'))
                        ->subject('Mail Configuration Test');
            });
            return 'Mail sent successfully! Check your inbox.';
        } catch (\Exception $e) {
            return 'Mail error: ' . $e->getMessage();
        }
    });

    /**
     * QR Login routes (accessible to everyone)
     */
    Route::prefix('login/qr')->controller(QrLoginController::class)->group(function () {
        Route::post('prepare', 'prepare')->name('shop.customer.login.qr.prepare');
        Route::post('check', 'checkStatus')->name('shop.customer.login.qr.check');
        Route::get('{token}', 'landing')->name('shop.customer.login.qr.landing')->middleware('signed');
        Route::post('{token}/authorize', 'authorizeLogin')->name('shop.customer.login.qr.authorize')->middleware(['auth:customer']);
    });

    /**
     * Guest routes (redirect logged-in users to account)
     */
    Route::group(['middleware' => [\Webkul\Shop\Http\Middleware\RedirectIfAuthenticated::class . ':customer']], function () {
        
        /**
         * Forgot password routes.
         * Disabled in favor of Seed Phrase recovery
         *
        Route::prefix('forgot-password')->controller(ForgotPasswordController::class)->group(function () {
            Route::get('', 'create')->name('shop.customers.forgot_password.create');
            Route::post('', 'store')->name('shop.customers.forgot_password.store');
        });
        */

        /**
         * Reset password routes.
         * Disabled
         *
        Route::prefix('reset-password')->controller(ResetPasswordController::class)->group(function () {
            Route::get('{token}', 'create')->name('shop.customers.reset_password.create');
            Route::post('', 'store')->name('shop.customers.reset_password.store');
        });
        */

    /**
     * Login routes.
     */
    Route::prefix('login')->controller(SessionController::class)->group(function () {
        Route::get('', 'index')->name('shop.customer.session.index');

        // Standard and Magic Link login methods disabled in favor of Passkeys
        // Route::post('', 'store')->name('shop.customer.session.create');
        // Route::post('email', 'sendLoginEmail')->name('shop.customer.session.email');
        // Route::get('link/{token}', 'loginByLink')->name('shop.customer.login.link');
        // Route::get('verify-identity', 'showVerifyIdentity')->name('shop.customer.login.verify_identity');
        // Route::post('verify-identity', 'verifyIdentity')->name('shop.customer.login.verify_identity.post');


    });

    /**
     * Passkey routes.
     */
    Route::post('passkeys/login', [PasskeyController::class, 'login'])->name('passkeys.login');

    /**
     * Telegram Mini App routes.
     */
    Route::post('tma/login', [\Webkul\Shop\Http\Controllers\Customer\TmaController::class, 'login'])->name('shop.tma.login');

    /**
     * Registration routes.
     */
    Route::controller(RegistrationController::class)->group(function () {
        Route::group(['prefix' => 'register'], function () {
            Route::get('', 'index')->name('shop.customers.register.index');

            Route::post('check-username', 'checkUsernameAvailability')->name('shop.customers.register.check_username');

            Route::post('passkey/prepare', 'passkeyPrepare')->name('shop.customers.register.passkey.prepare');

            Route::post('passkey/prepare-other', 'passkeyPrepareOtherDevice')->name('shop.customers.register.passkey.prepare_other');

            Route::post('check-status', 'checkRegistrationStatus')->name('shop.customers.register.check_status');

            Route::post('', 'store')->name('shop.customers.register.store');
        });

        /**
         * Customer verification routes (Disabled - all users verified on registration)
         *
        Route::get('verify-account/{token}', 'verifyAccount')->name('shop.customers.verify');
        Route::get('verify-code', 'showVerifyCode')->name('shop.customers.verify.code');
        Route::post('verify-code', 'verifyByCode')->name('shop.customers.verify.code.submit');
        Route::get('resend/verification/{email}', 'resendVerificationEmail')->name('shop.customers.resend.verification_email');
        */
    });



    /**
     * Recovery routes.
     */
    Route::prefix('recovery')->controller(\Webkul\Shop\Http\Controllers\Customer\RecoveryController::class)->group(function () {
        Route::get('seed', 'showSeedForm')->name('shop.customers.recovery.seed');
        Route::post('seed', 'recoverBySeed')->name('shop.customers.recovery.seed.post');
    });

    }); // End of guest routes group
 
    /**
     * Passkey login options (accessible to both guests and authenticated users for step-up auth)
     */
    Route::post('passkeys/login-options', [PasskeyController::class, 'loginOptions'])->name('passkeys.login-options');

    /**
     * Customer authenticated routes. All the below routes only be accessible
     * if customer is authenticated.
     */
    /**
     * Customer authenticated routes. All the below routes only be accessible
     * if customer is authenticated.
     */
    /**
     * Passkey linking (landing page) needs to be accessible via signed link without existing session.
     */
    Route::get('passkeys/link', [PasskeyController::class, 'linkLanding'])
        ->name('shop.customers.account.passkeys.link')
        ->middleware('signed');

    /**
     * Phone landing route for registration flow (must be outside guest group to avoid RedirectIfAuthenticated)
     */
    Route::get('register/phone/{token}', [RegistrationController::class, 'registrationPhoneLanding'])
        ->name('shop.customers.register.phone.landing')
        ->middleware(['signed', NoCacheMiddleware::class]);

    Route::get('register/phone/{token}/mark-continuing', [RegistrationController::class, 'markAsContinuing'])
        ->name('shop.customers.register.phone.mark_continuing')
        ->middleware(['signed', NoCacheMiddleware::class]);

    // Passkey registration routes — accessible WITHOUT being logged in
    // (user may be pre-created but not yet authenticated; controller uses link_user_id session)
    Route::controller(PasskeyController::class)->prefix('passkeys')->group(function () {
        Route::post('register-options', 'registerOptions')->name('passkeys.register-options');
        Route::post('register', 'register')->name('passkeys.register');
    });

    Route::group(['middleware' => ['customer']], function () {
        Route::group(['middleware' => [NoCacheMiddleware::class]], function () {
            /**
             * Datagrid routes.
             */
            Route::get('datagrid/look-up', [DataGridController::class, 'lookUp'])->name('shop.customer.datagrid.look_up');

            /**
             * Passkey authenticated routes.
             */
            Route::prefix('passkeys')->controller(PasskeyController::class)->group(function () {
                Route::get('', 'index')->name('shop.customers.account.passkeys.index');
                Route::get('generate-link', 'generateLink')->name('shop.customers.account.passkeys.generate-link');
                Route::delete('{id}', 'destroy')->name('passkeys.destroy');
            });

            /**
             * Login activity routes.
             */
            Route::prefix('login-activity')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\LoginHistoryController::class)->group(function () {
                Route::get('', 'index')->name('shop.customers.account.login_activity.index');
                Route::delete('{id}', 'destroy')->name('shop.customers.account.login_activity.destroy');
            });

            /**
             * Logout.
             */
            Route::get('logout', [SessionController::class, 'destroy'])->name('shop.customer.session.destroy.get');
            Route::delete('logout', [SessionController::class, 'destroy'])->name('shop.customer.session.destroy');
        });

        /**
         * Customer account. All the below routes are related to
         * customer account details.
         */
        Route::group(['prefix' => 'account'], function () {
            Route::get('', [CustomerController::class, 'account'])->name('shop.customers.account.index');
            Route::get('security', [CustomerController::class, 'showSecurity'])->name('shop.customers.account.security.index');
            Route::get('security/telegram-token', [CustomerController::class, 'generateTelegramToken'])->name('shop.customers.account.security.telegram_token');
            Route::get('security-onboarding', [CustomerController::class, 'showSecurityOnboarding'])->name('shop.customers.account.onboarding.security');
            // Email onboarding disabled
            // Route::get('security-onboarding/add-email', [CustomerController::class, 'showAddEmail'])->name('shop.customers.account.onboarding.add_email');
            // Route::post('security-onboarding/add-email', [CustomerController::class, 'sendEmailVerificationCode'])->name('shop.customers.account.onboarding.add_email.post');
            // Route::get('security-onboarding/verify-email', [CustomerController::class, 'showVerifyEmailView'])->name('shop.customers.account.onboarding.verify_email_view');
            // Route::post('security-onboarding/verify-email', [CustomerController::class, 'verifyEmailCode'])->name('shop.customers.account.onboarding.verify_email.post');

            Route::group(['middleware' => [NoCacheMiddleware::class]], function () {
                /**
                 * Wallet Access (Passkey) Setup & Unlock
                 */
                Route::prefix('wallet-access')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\WalletAccessController::class)->group(function () {
                    Route::get('setup', 'setup')->name('shop.customers.account.wallet.setup');
                });

                /**
                 * Credits (formerly Transactions).
                 */
                Route::group([], function () {
                    Route::get('credits/lookup', [\Webkul\Shop\Http\Controllers\Customer\Account\RecipientLookupController::class, 'lookup'])->name('shop.customers.account.credits.lookup');
                    Route::post('crypto/send', [\Webkul\Shop\Http\Controllers\Customer\Account\CryptoSendController::class, 'store'])->name('shop.customers.account.crypto.send');
                    Route::get('credits', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'index'])->name('shop.customers.account.credits.index');
                    Route::get('credits/transactions', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'transactions'])->name('shop.customers.account.credits.transactions');
                    Route::get('credits/deposit', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'deposit'])->name('shop.customers.account.credits.deposit');

                    Route::post('credits/invoice', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'storeInvoice'])->name('shop.customers.account.credits.invoice.store');
                    Route::get('credits/invoice/print/{id}', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'printInvoice'])->name('shop.customers.account.credits.invoice.print');
                    Route::post('credits/invoice/email/{id}', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'emailInvoice'])->name('shop.customers.account.credits.invoice.email');
                    Route::get('credits/organizations/{id}/bank-accounts', [\Webkul\Shop\Http\Controllers\Customer\Account\CreditController::class, 'getBankAccounts'])->name('shop.customers.account.credits.organizations.bank_accounts');

                    // Credits Transfer
                    Route::prefix('credits')->controller(TransferController::class)->group(function () {
                        Route::post('transfer', 'store')->name('shop.customers.account.credits.transfer');
                    });

                    /**
                     * Handshakes (Peer-to-peer social connections).
                     */
                    Route::prefix('handshakes')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\HandshakeController::class)->group(function () {
                        Route::get('', 'index')->name('shop.customers.account.handshakes.index');
                        Route::post('ping', 'ping')->name('shop.customers.account.handshakes.ping');
                        Route::post('acknowledge/{id}', 'acknowledge')->name('shop.customers.account.handshakes.acknowledge');
                        Route::delete('terminate/{id}', 'terminate')->name('shop.customers.account.handshakes.terminate');
                    });

                    Route::get('test-route', function () {
                        return 'Customer account group is working!';
                    });

                    /**
                     * Redeem.
                     */
                    Route::prefix('redeem')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\RedeemController::class)->group(function () {
                        Route::get('', 'index')->name('shop.customers.account.redeem.index');
                        Route::post('verify', 'verify')->name('shop.customers.account.redeem.verify');
                        Route::post('send-verification', 'sendVerification')->name('shop.customers.account.redeem.send_verification');
                        Route::post('activate', 'activate')->name('shop.customers.account.redeem.activate');
                    });
                });
                Route::controller(CustomerController::class)->group(function () {
                    Route::group(['prefix' => 'profile'], function () {
                        Route::get('edit', 'edit')->name('shop.customers.account.profile.edit');

                        Route::get('recovery-key', 'recoveryKey')->name('shop.customers.account.profile.recovery_key');

                        Route::get('generate-recovery-key', 'generateRecoveryKey')->name('shop.customers.account.profile.generate_recovery_key');

                        Route::get('verify-recovery-key', 'showVerifyRecoveryKey')->name('shop.customers.account.profile.verify_recovery_key');
                        Route::post('verify-recovery-key', 'verifyRecoveryKey')->name('shop.customers.account.profile.verify_recovery_key.post');

                        Route::get('complete-registration-success', 'completeRegistrationSuccess')->name('shop.customers.account.profile.complete_registration_success');

                        Route::get('check-username', 'checkUsername')->name('shop.customers.account.profile.check_username');

                        Route::post('toggle-newsletter', 'toggleNewsletter')->name('shop.customers.account.profile.toggle_newsletter');

                        Route::post('edit', 'update')->name('shop.customers.account.profile.update');

                        Route::post('destroy', 'destroy')->name('shop.customers.account.profile.destroy');
                    });

                    Route::get('reviews', 'reviews')->name('shop.customers.account.reviews.index');
                });

                // Credits Transfer
                Route::controller(TransferController::class)->prefix('credits')->group(function () {
                    Route::post('transfer', 'store')->name('shop.customers.account.credits.transfer');
                });

                /**
                 * GDPR.
                 */
                Route::prefix('gdpr')->controller(GDPRController::class)->group(function () {
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
                Route::prefix('addresses')->controller(AddressController::class)->group(function () {
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
                Route::prefix('orders')->controller(OrderController::class)->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.orders.index');

                    Route::get('view/{id}', 'view')->name('shop.customers.account.orders.view');

                    Route::get('reorder/{id}', 'reorder')->name('shop.customers.account.orders.reorder');

                    Route::post('cancel/{id}', 'cancel')->name('shop.customers.account.orders.cancel');

                    Route::get('print/Invoice/{id}', 'printInvoice')->name('shop.customers.account.orders.print-invoice');
                });

                /**
                 * Downloadable products.
                 */
                Route::prefix('downloadable-products')->controller(DownloadableProductController::class)->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.downloadable_products.index');

                    Route::get('download/{id}', 'download')->name('shop.customers.account.downloadable_products.download');
                });

                /**
                 * Magic AI routes.
                 */
                Route::prefix('magic-ai')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\MagicAIController::class)->group(function () {
                    Route::post('parse-bank-details', 'parseBankDetails')->name('shop.customers.account.magic_ai.parse_bank_details');
                });

                /**
                 * Organizations.
                 */
                Route::prefix('organizations')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\OrganizationController::class)->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.organizations.index');

                    Route::get('create', 'create')->name('shop.customers.account.organizations.create');

                    Route::post('create', 'store')->name('shop.customers.account.organizations.store');

                    Route::get('edit/{id}', 'edit')->name('shop.customers.account.organizations.edit');

                    Route::put('edit/{id}', 'update')->name('shop.customers.account.organizations.update');

                    Route::delete('delete/{id}', 'destroy')->name('shop.customers.account.organizations.delete');

                    Route::post('{id}/settlement-accounts', 'storeSettlementAccount')->name('shop.customers.account.organizations.settlement_accounts.store');

                    Route::put('{organizationId}/settlement-accounts/{accountId}/alias', 'updateSettlementAccountAlias')->name('shop.customers.account.organizations.settlement_accounts.update_alias');

                    Route::delete('{organizationId}/settlement-accounts/{accountId}', 'destroySettlementAccount')->name('shop.customers.account.organizations.settlement_accounts.destroy');

                    Route::get('lookup-inn/{inn}', 'lookupInn')->name('shop.customers.account.organizations.lookup_inn');

                    Route::get('lookup-bic/{bic}', 'lookupBic')->name('shop.customers.account.organizations.lookup_bic');

                    Route::get('suggest-bank', 'suggestBank')->name('shop.customers.account.organizations.suggest_bank');

                    Route::get('suggest', 'suggestOrganization')->name('shop.customers.account.organizations.suggest');

                    Route::get('suggest-organization', 'suggestOrganization')->name('shop.customers.account.organizations.suggest_organization');
                });

                /**
                 * Crypto Wallets.
                 */
                Route::prefix('crypto')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\CryptoController::class)->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.crypto.index');
                    Route::get('upgrade-wallet', 'showUpgradeWallet')->name('shop.customers.account.crypto.show_upgrade_wallet');
                    Route::post('upgrade-wallet', 'upgradeWallet')->name('shop.customers.account.crypto.upgrade_wallet');

                    Route::post('create', 'store')->name('shop.customers.account.crypto.store');

                    Route::get('sync/{id}', 'sync')->name('shop.customers.account.crypto.sync');

                    Route::get('verify/{id}', 'verify')->name('shop.customers.account.crypto.verify');

                    Route::post('update-alias/{id}', 'updateAlias')->name('shop.customers.account.crypto.update_alias');
                    Route::delete('delete/{id}', 'destroy')->name('shop.customers.account.crypto.delete');
                });
                /**
                 * Calls.
                 */
                Route::prefix('calls')->controller(\Webkul\Shop\Http\Controllers\Customer\Account\CallController::class)->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.calls.index');
                    Route::post('signal', 'signal')->name('shop.customers.account.calls.signal');
                });

                /**
                 * Matrix / Hydrogen Integration
                 */
                Route::controller(\Webkul\Shop\Http\Controllers\Customer\Account\MatrixController::class)->prefix('matrix')->group(function () {
                    Route::get('', 'index')->name('shop.customers.account.matrix.index');
                    Route::post('sync', 'sync')->name('shop.customers.account.matrix.sync');
                });

            });
        });
    });

    Route::post('telegram/webhook', [\Webkul\Shop\Http\Controllers\Customer\TelegramBotController::class, 'webhook'])->name('shop.telegram.webhook');
});
