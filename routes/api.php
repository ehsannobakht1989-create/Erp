<?php

declare(strict_types=1);

use App\Http\Controllers\Core\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication endpoints are the only public/core API routes; module routes require can: abilities.
Route::prefix('core/auth')->name('core.auth.')->group(function (): void {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:core-login')->name('login');
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
