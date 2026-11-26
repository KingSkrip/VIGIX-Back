<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Dashboard\DashboardDataController;
use App\Http\Middleware\ValidateJwtSession;
use Illuminate\Support\Facades\Route;

// ==========================================
// AUTH
// ==========================================

Route::prefix('auth')->group(function () {

    // Login
    Route::post('/login', [LoginController::class, 'login']);

    // Rutas protegidas de autenticación
    Route::middleware([
        'auth:api',
        ValidateJwtSession::class,
    ])->group(function () {

        // Logout
        Route::post('/logout', [LoginController::class, 'logout']);
    });
});

// ==========================================
// DASHBOARD
// ==========================================

Route::prefix('dash')
    ->middleware([
        'auth:api',
        ValidateJwtSession::class,
    ])
    ->group(function () {

        // Recuperar usuario y validar sesión
        Route::get('/me', [DashboardDataController::class, 'me']);
    });