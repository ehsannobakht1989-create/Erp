<?php

declare(strict_types=1);

use App\Http\Controllers\Core\InstallController;
use App\Support\Install\Installer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

// Loaded without the web group: no sessions or cookie encryption exist before APP_KEY is generated.
Route::get('install', [InstallController::class, 'show'])->name('core.install.show');
Route::post('install', [InstallController::class, 'store'])->middleware('throttle:core-install')->name('core.install.store');

// Entry point: send uninstalled deployments to the installer. The React shell is served here from Phase 2.
Route::get('/', fn (Installer $installer) => $installer->locked()
    ? new Response('<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>ERP</title><p>سامانه نصب شده است. رابط کاربری در فاز ۲ اضافه می‌شود؛ API در مسیر /api در دسترس است.</p></html>', 200, ['Content-Type' => 'text/html; charset=UTF-8'])
    : new RedirectResponse('/install'))->name('core.home');
