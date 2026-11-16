<?php

declare(strict_types=1);

use App\Http\Controllers\Core\InstallController;
use Illuminate\Support\Facades\Route;

// Loaded without the web group: no sessions or cookie encryption exist before APP_KEY is generated.
Route::get('install', [InstallController::class, 'show'])->name('core.install.show');
Route::post('install', [InstallController::class, 'store'])->middleware('throttle:core-install')->name('core.install.store');
