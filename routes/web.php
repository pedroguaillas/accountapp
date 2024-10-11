<?php

use App\Http\Controllers\Landlord\AuthController;
use App\Http\Controllers\Landlord\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

foreach (config('tenancy.central_domains') as $domain) {

    Route::domain($domain)->group(function () {

        // Login para todos los usuarios
        Route::post('login2', [AuthController::class, 'login'])->name('login2');

        Route::get('/', function () {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        });

        Route::middleware([
            'auth:sanctum',
            config('jetstream.auth_session'),
            'verified',
        ])->group(function () {

            Route::get('/debug', function () {
                return response()->json([
                    'user' => Auth::user(),
                    'session' => session()->all(), // Ver contenido de la sesión
                    'tenant' => tenant('id') // Verificar si el tenant está inicializado
                ]);
            });

            // Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

            Route::get('/dashboard', function () {
                return Inertia::render('Dashboard');
            })->name('dashboard');

            // Nuestros clientes
            Route::get('clientes', [UserController::class, 'index'])->name('customers.index');
            Route::post('customers', [UserController::class, 'store'])->name('customers.store');
        });
    });

}