<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\ConfigurationController;

/**
 * Configuration routes.
 */
Route::get('configuration/search', [ConfigurationController::class, 'search'])->name('admin.configuration.search');

Route::get('configuration/download/{path}', [ConfigurationController::class, 'download'])->name('admin.configuration.download');

Route::get('configuration/{slug?}/{slug2?}', [ConfigurationController::class, 'index'])->name('admin.configuration.index');

Route::post('configuration/{slug?}/{slug2?}', [ConfigurationController::class, 'store'])->name('admin.configuration.store');
