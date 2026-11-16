<?php

declare(strict_types=1);

use App\Http\Controllers\Core\AuthController;
use App\Http\Controllers\Core\MetaController;
use Illuminate\Support\Facades\Route;

// Login is the only public API route; me/logout/meta need only authentication. Module routes require can: abilities.
Route::prefix('core/auth')->name('core.auth.')->group(function (): void {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:core-login')->name('login');
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::get('core/meta', MetaController::class)->middleware(['auth:sanctum', 'company.context'])->name('core.meta');
