<?php

declare(strict_types=1);

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\Landlord\AuthController;
use App\Http\Middleware\ShareGlobalData;
use App\Http\Middleware\SwitchToCentralDatabase;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\ScopeSessions;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {

    Route::get('/debug', function () {
        return response()->json([
            'user' => Auth::user(),
            'session' => session()->all(), // Ver contenido de la sesión
            'cookies' => $_COOKIE, // Muestra todas las cookies asociadas
            'tenant' => tenant('id') // Verificar si el tenant está inicializado
        ]);
    });

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {

        Route::get('/', function () {
            return Inertia::render('Dashboard');
        })->name('tenant.dashboard');

        Route::post('/logout', function () {
            // Redirigir al dominio principal para procesar el logout
            return redirect()->away(env('APP_URL') . '/logout');
        })->name('logout');

        // Centro de costos
        Route::get('rucs', [CompanyController::class, 'index'])->name('rucs.index');
        Route::post('rucs', [CompanyController::class, 'store'])->name('company.store');
        Route::get('centro-de-costos', [CostCenterController::class, 'index'])->name('costcenter.index');
        Route::post('costcenters', [CostCenterController::class, 'store'])->name('costcenter.store');
    });
});

// Route::group([
//     'prefix' => '/{tenant}',
//     'middleware' => [
//         InitializeTenancyByPath::class, 
//         // ScopeSessions::class
//     ],
// ], function () {
//     // Route::get('/foo', 'FooController@index');
//     Route::get('/', function () {
//         return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     });
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('tenant.dashboard');
//     Route::get('centro-de-costos', [CostCenterController::class, 'index'])->name('costcenter.index');
//     Route::post('costcenters', [CostCenterController::class, 'store'])->name('costcenter.store');

//     Route::get('/debug', function () {
//         return response()->json([
//             'user' => Auth::user(),
//             'session' => session()->all(), // Ver contenido de la sesión
//             'cookies' => $_COOKIE, // Muestra todas las cookies asociadas
//             'tenant' => tenant('id') // Verificar si el tenant está inicializado
//         ]);
//     });
// });