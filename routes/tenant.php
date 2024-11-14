<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\JournalController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\ScopeSessions;

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

        // Midleware can all
        // Midleware can vendor ventas
        Route::get('/', function () {
            return Inertia::render('Dashboard');
        })->name('tenant.dashboard');

        Route::post('/logout', function () {
            // Redirigir al dominio principal para procesar el logout
            return redirect()->away(env('APP_URL') . '/logout');
        })->name('logout');

       //companias
        Route::get('rucs', [CompanyController::class, 'index'])->name('rucs.index');
        Route::post('rucs', [CompanyController::class, 'store'])->name('company.store');
        Route::put('rucs/{company}', [CompanyController::class, 'update'])->name('company.update');
           
    
    
        // Centro de costos
        Route::get('centro-de-costos', [CostCenterController::class, 'index'])->name('costcenter.index');
        Route::post('costcenters', [CostCenterController::class, 'store'])->name('costCenter.store');
        Route::put('costcenters/{costCenter}', [CostCenterController::class,'update'])->name('costCenter.update');
        Route::delete('costcenters/{costCenter}', [CostCenterController::class, 'delete'])->name('costCenter.delete');

        

        //establecimientos
        Route::get('establecimientos', [BranchController::class,'index'])->name('branch.index');
        Route::post('stores', [BranchController::class,'store'])->name('branch.store');
        Route::put('stores/{branch}', [BranchController::class,'update'])->name('branch.update');
        Route::delete('stores/{branch}', [BranchController::class, 'delete'])->name('branch.delete');

        // Contabilidad
        Route::get('plan-de-cuentas', [AccountController::class, 'index'])->name('accounts');
        Route::post('accounts/import', [AccountController::class, 'import'])->name('accounts.import');

        // Journals
        Route::get('asientos', [JournalController::class,'index'])->name('journal.index');
        Route::get('asiento/crear', [JournalController::class,'create'])->name('journal.create');
        Route::post('journals', [JournalController::class,'store'])->name('journal.store');
        Route::put('journals/{journal}', [JournalController::class,'update'])->name('journal.update');
        Route::delete('journals/{journal}', [JournalController::class, 'delete'])->name('journal.delete');

    });
});