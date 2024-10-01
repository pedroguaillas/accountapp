<?php

declare(strict_types=1);

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
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('tenant.dashboard');

    // Route::post('logout', 'AuthController@logout')->name('tenant.logout');
    Route::post('/logout', function () {
        // Redirigir al dominio principal para procesar el logout
        return redirect()->away(env('APP_URL') . '/logout');
    })->name('logout');

    // Centro de costos
    Route::get('centro-de-costos', [CostCenterController::class, 'index'])->name('costcenter.index');
    Route::post('costcenters', [CostCenterController::class, 'store'])->name('costcenter.store');
});

// Route::group([
//     'prefix' => '/{tenant}',
//     'middleware' => [ShareGlobalData::class, InitializeTenancyByPath::class],
// ], function () {
//     // Route::get('/foo', 'FooController@index');
//     Route::get('/', function () {
//         return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     });
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('tenant.dashboard');
// });