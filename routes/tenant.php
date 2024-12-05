<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\IntangibleAssetController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\DepreciationController;
use App\Http\Controllers\AmortizationController;
use App\Http\Controllers\AssetManagementController;
use App\Http\Controllers\IntangibleManagementController;
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
            // Revocar todos los tokens del usuario
            // $request->user()->currentAccessToken()->delete();

            // Limpiar sesión y revocar tokens
            Auth::guard('web')->logout();

            // $request->session()->invalidate();
            // $request->session()->regenerateToken();

            // Redirigir al dominio principal
            return Inertia::location(env('APP_URL'));
        })->name('logout');

        //companias
        Route::get('rucs', [CompanyController::class, 'index'])->name('rucs.index');
        Route::post('rucs', [CompanyController::class, 'store'])->name('company.store');
        Route::put('rucs/{company}', [CompanyController::class, 'update'])->name('company.update');

        // Centro de costos
        Route::get('centro-de-costos', [CostCenterController::class, 'index'])->name('costcenter.index');
        Route::post('costcenters', [CostCenterController::class, 'store'])->name('costCenter.store');
        Route::put('costcenters/{costCenter}', [CostCenterController::class, 'update'])->name('costCenter.update');
        Route::delete('costcenters/{costCenterId}', [CostCenterController::class, 'destroy'])->name('costCenter.delete');

        //establecimientos
        Route::get('establecimientos', [BranchController::class, 'index'])->name('branch.index');
        Route::post('stores', [BranchController::class, 'store'])->name('branch.store');
        Route::put('stores/{branch}', [BranchController::class, 'update'])->name('branch.update');
        Route::delete('stores/{branchId}', [BranchController::class, 'destroy'])->name('branch.delete');

        // Contabilidad
        Route::get('plan-de-cuentas', [AccountController::class, 'index'])->name('accounts');
        Route::post('accounts/import', [AccountController::class, 'import'])->name('accounts.import');
        Route::resource('account', AccountController::class)->only(['store', 'update', 'delete']);

        // Journals
        Route::get('asientos', [JournalController::class, 'index'])->name('journal.index');
        Route::get('asiento/crear', [JournalController::class, 'create'])->name('journal.create');
        Route::post('journals', [JournalController::class, 'store'])->name('journal.store');

        // fixed assets  //poner las rutas con nombre en plural 
        Route::get('activos-fijos', [FixedAssetController::class, 'index'])->name('fixedassets.index');
        Route::get('activos-fijos/crear', [FixedAssetController::class, 'create'])->name('fixedassets.create');
        Route::post('fixedassets', [FixedAssetController::class, 'store'])->name('fixedassets.store');
        Route::get('activos-fijos/editar/{fixedAssetId}', [FixedAssetController::class, 'edit'])->name('fixedassets.edit');
        Route::put('fixedassets/{fixedAsset}', [FixedAssetController::class, 'update'])->name('fixedassets.update');
        Route::delete('fixedassets/{fixedAssetId}', [FixedAssetController::class, 'destroy'])->name('fixedassets.delete');

        Route::get('/depreciacion', [DepreciationController::class, 'index'])->name('depreciations.index');

        Route::get('/activos-depreciacion', [AssetManagementController::class, 'index'])->name('assetsdepreciation.index');


        // intngible assets  //poner las rutas con nombre en plural 
        Route::get('activos-intangibles', [IntangibleAssetController::class, 'index'])->name('intangibleassets.index');
        Route::get('activos-intangibles/crear', [IntangibleAssetController::class, 'create'])->name('intangibleassets.create');
       Route::post('intangibleassets', [IntangibleAssetController::class, 'store'])->name('intangibleassets.store');
        Route::get('activos-intangible/editar/{intangibleAssetId}', [IntangibleAssetController::class, 'edit'])->name('intangibleassets.edit');
        Route::put('intangibleassets/{intangibleasset}', [IntangibleAssetController::class, 'update'])->name('intangibleassets.update');
        Route::delete('intangibleassets/{intangibleasset}', [IntangibleAssetController::class, 'destroy'])->name('intangibleassets.delete');

        Route::get('/amortizacion', [AmortizationController::class, 'index'])->name('amortizations.index');


        Route::get('/activos-amortizacion', [IntangibleManagementController::class, 'index'])->name('intangibleamortization.index');


        //resumir rutas
       // Route::resource('intangibleassets', IntangibleAssetController::class)->only(['store','update']);

    });
});