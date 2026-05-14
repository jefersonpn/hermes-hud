<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HudController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('hud') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/hud', [HudController::class, 'index'])->name('hud');

    Route::prefix('hud/api')->group(function () {
        Route::get('system',          [HudController::class, 'system']);
        Route::get('hermes/status',   [HudController::class, 'hermesStatus']);
        Route::get('hermes/logs',     [HudController::class, 'hermesLogs']);
        Route::get('hermes/gateway',  [HudController::class, 'hermesGateway']);
    });
});
