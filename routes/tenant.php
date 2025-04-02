<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdvanceController;
use App\Http\Controllers\HourController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\DepreciationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymentRoleController;
use App\Http\Controllers\AccountLinkController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\IntangibleAssetController;
use App\Http\Controllers\RoleIngressController;
use App\Http\Controllers\RoleEgressController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionBoxController;
use App\Http\Controllers\TransactionExpenseController;

use App\Http\Controllers\Landlord\PaymentRegimController;
use App\Http\Controllers\Landlord\IessController;
use App\Http\Controllers\Landlord\MovementTypeController;
use App\Http\Controllers\Landlord\IvaController;
use App\Http\Controllers\Landlord\WithholdingController;
use App\Http\Controllers\Landlord\IceController;
use App\Http\Controllers\Landlord\PayMethodController;
use App\Http\Controllers\Landlord\ExpenseController;

use App\Http\Controllers\Setting\AccountController as SettingAccountController;
use App\Http\Controllers\Setting\RolController as SettingRolController;
use App\Http\Controllers\Setting\BankController as SettingBankController;
use App\Http\Controllers\Setting\BoxController as SettingBoxController;
use App\Http\Controllers\Setting\ExpenseController as SettingExpenseController;

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

    // Route::get('/login', function () {
    //     return Inertia::location(env('APP_URL'));
    // })->name('tenant.login');

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
        Route::delete('rucs/{companyId}', [CompanyController::class, 'destroy'])->name('company.delete');

        // Centro de costos
        Route::get('centro-de-costos', [CostCenterController::class, 'index'])->name('costcenter.index');
        Route::post('costcenters', [CostCenterController::class, 'store'])->name('costCenter.store');
        Route::put('costcenters/{costCenter}', [CostCenterController::class, 'update'])->name('costCenter.update');
        Route::delete('costcenters/{costCenterId}', [CostCenterController::class, 'destroy'])->name('costCenter.delete');
        Route::put('costcenters/{id}/state', [CostCenterController::class, 'updateState'])->name('costCenter.updateState');

        //establecimientos
        Route::get('establecimientos', [BranchController::class, 'index'])->name('branch.index');
        Route::post('stores', [BranchController::class, 'store'])->name('branch.store');
        Route::put('stores/{branch}', [BranchController::class, 'update'])->name('branch.update');
        Route::delete('stores/{branch}', [BranchController::class, 'destroy'])->name('branch.delete');
        Route::put('stores/{id}/state', [BranchController::class, 'updateState'])->name('branch.updateState');

        // Contabilidad
        Route::get('plan-de-cuentas', [AccountController::class, 'index'])->name('accounts');
        Route::post('accounts/import', [AccountController::class, 'import'])->name('accounts.import');
        Route::resource('accounts', AccountController::class)->only(['store', 'update', 'destroy']);
        Route::get('accounts/create/{account}', [AccountController::class, 'create'])->name('accounts.create');

        // Journals
        Route::get('asientos', [JournalController::class, 'index'])->name('journal.index');
        Route::get('asiento/crear', [JournalController::class, 'create'])->name('journal.create');
        Route::get('asiento/editar/{journalId}', [JournalController::class, 'edit'])->name('journal.edit');
        Route::delete('journal/{journal}', [JournalController::class, 'destroy'])->name('journal.delete');
        Route::resource('journal', JournalController::class)->only(['store', 'update']);

        // fixed assets  //poner las rutas con nombre en plural 
        Route::get('activos-fijos', [FixedAssetController::class, 'index'])->name('fixedassets.index');
        Route::get('activos-fijos/crear', [FixedAssetController::class, 'create'])->name('fixedassets.create');
        Route::post('fixedassets', [FixedAssetController::class, 'store'])->name('fixedassets.store');
        Route::get('activos-fijos/editar/{fixedAssetId}', [FixedAssetController::class, 'edit'])->name('fixedassets.edit');
        Route::put('fixedassets/{fixedAsset}', [FixedAssetController::class, 'update'])->name('fixedassets.update');
        Route::delete('fixedassets/{fixedAsset}', [FixedAssetController::class, 'destroy'])->name('fixedassets.delete');

        // Depreciaciones
        Route::get('depreciaciones/{fixedAsset}', [DepreciationController::class, 'index'])->name('depreciations.index');

        // intngible assets  //poner las rutas con nombre en plural 
        Route::get('activos-intangibles', [IntangibleAssetController::class, 'index'])->name('intangibleassets.index');
        Route::get('activos-intangibles/crear', [IntangibleAssetController::class, 'create'])->name('intangibleassets.create');
        Route::post('intangibleassets', [IntangibleAssetController::class, 'store'])->name('intangibleassets.store');
        Route::get('activos-intangible/editar/{intangibleAssetId}', [IntangibleAssetController::class, 'edit'])->name('intangibleassets.edit');
        Route::put('intangibleassets/{intangibleasset}', [IntangibleAssetController::class, 'update'])->name('intangibleassets.update');
        Route::delete('intangibleassets/{intangibleasset}', [IntangibleAssetController::class, 'destroy'])->name('intangibleassets.delete');

        //empleados
        Route::get('empleados', [EmployeeController::class, 'index'])->name('employee.index');
        Route::get('empleados/crear', [EmployeeController::class, 'create'])->name('employee.create');
        Route::get('empleados/editar/{employeeId}', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employee.delete');
        Route::resource('employee', EmployeeController::class)->only(['store', 'update']);
        Route::put('employees/{id}/state', [EmployeeController::class, 'updateState'])->name('employee.updateState');
        Route::get('employees', [EmployeeController::class, 'getEmployees'])->name('employees.filters.index');

        //adelantos
        Route::get('adelantos', [AdvanceController::class, 'index'])->name('advances.index');
        Route::get('adelantos/crear', [AdvanceController::class, 'create'])->name('advances.create');
        Route::post('advances', [AdvanceController::class, 'store'])->name('advances.store');
        Route::put('advances/{advance}', [AdvanceController::class, 'update'])->name('advances.update');
        Route::delete('advances/{advance}', [AdvanceController::class, 'destroy'])->name('advances.delete');

        //roles
        Route::get('roles-de-pago', [PaymentRoleController::class, 'index'])->name('paymentrol.index');
        Route::put('paymentroles/{id}', [PaymentRoleController::class, 'update'])->name('paymentrol.update');
        Route::get('paymentroles/export', [PaymentRoleController::class, 'export'])->name('paymentrol.export');
        Route::post('generaterolesjournals', [PaymentRoleController::class, 'generate'])->name('paymentrol.journal.generate');

        // ingresos
        Route::get('roles-ingresos', [RoleIngressController::class, 'index'])->name('roleingress.index');
        Route::delete('roleingresses/{ingressId}', [RoleIngressController::class, 'destroy'])->name('roleingress.delete');
        Route::resource('roleingresses', RoleIngressController::class)->only(['store', 'update']);

        // egresos
        Route::get('roles-egresos', [RoleEgressController::class, 'index'])->name('roleegress.index');
        Route::delete('roleegresses/{egressId}', [RoleEgressController::class, 'destroy'])->name('roleegress.delete');
        Route::resource('roleegresses', RoleEgressController::class)->only(['store', 'update']);

        //horas
        Route::get('horas', [HourController::class, 'index'])->name('hours.index');
        Route::post('hours', [HourController::class, 'store'])->name('hours.store');
        Route::put('hours/{hour}', [HourController::class, 'update'])->name('hours.update');
        Route::delete('hours/{hour}', [HourController::class, 'destroy'])->name('hours.delete');

        // Ajustes de empresa
        Route::get('empresa/ajuste/roles', [BusinessController::class, 'roles'])->name('business.setting.roles');
        Route::get('settingsrole', [BusinessController::class, 'roles'])->name('business.setting.roles');


        // Vinculacion general de cuentas
        Route::get('empresa/ajuste/vinculacioncuentas', [AccountLinkController::class, 'roles'])->name('business.setting.accountlinks');
        Route::get('settingsaccountlink', [AccountLinkController::class, 'roles'])->name('business.setting.accountlinks');

        // Emparejamiento de cuentas activo fijo
        Route::get('vinculacion-contable/activos-fijos', [SettingAccountController::class, 'index'])->name('setting.account.index');
        Route::put('settingaccount/update/{activeType}', [SettingAccountController::class, 'updateActiveTypeAccount'])->name('settingaccount.update');

        // Emparejamiento de cuentas roles
        Route::get('vinculacion-contable/roles', [SettingRolController::class, 'index'])->name('setting.account.rol.index');
        Route::put('settingaccountrol/update/{id}', [SettingRolController::class, 'updateRoleAccount'])->name('settingaccount.rol.update');

        // Emparejamiento de cuentas bancos
        Route::get('vinculacion-contable/bancos', [SettingBankController::class, 'index'])->name('setting.account.bank.index');
        Route::put('settingaccountbank/update/{id}', [SettingBankController::class, 'updateBankAccount'])->name('settingaccount.bank.update');

        // Emparejamiento de cuentas roles
        Route::get('vinculacion-contable/cajas', [SettingBoxController::class, 'index'])->name('setting.account.box.index');
        Route::put('settingaccountbox/update/{id}', [SettingBoxController::class, 'updateBoxAccount'])->name('settingaccount.box.update');

        // Emparejamiento de cuentas roles
        Route::get('vinculacion-contable/gastos', [SettingExpenseController::class, 'index'])->name('setting.account.expenses.index');
        Route::put('settingaccountexpense/update/{id}', [SettingExpenseController::class, 'updateExpenseAccount'])->name('settingaccount.expenses.update');

        //bancos
        Route::get('bancos', [BankController::class, 'index'])->name('banks.index');
        Route::post('banks', [BankController::class, 'store'])->name('banks.store');
        Route::put('banks/{bank}', [BankController::class, 'update'])->name('banks.update');
        Route::delete('banks/{bank}', [BankController::class, 'destroy'])->name('banks.delete');
        Route::put('banks/{id}/state', [BankController::class, 'updateState'])->name('banks.updateState');

        //cuentas
        Route::get('cuentas-bancarias/{bank}', [BankAccountController::class, 'index'])->name('bankaccounts.index');
        Route::post('bankaccounts', [BankAccountController::class, 'store'])->name('bankaccounts.store');
        Route::put('bankaccounts/{bankaccount}', [BankAccountController::class, 'update'])->name('bankaccounts.update');
        Route::delete('bankaccounts/{bankaccount}', [BankAccountController::class, 'destroy'])->name('bankaccounts.delete');
        Route::put('bankaccounts/{id}/state', [BankAccountController::class, 'updateState'])->name('bankaccounts.updateState');

        //transacciones
        Route::get('movimientos-bancarios', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('movimientos-bancarios/crear', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');

        //personas
        Route::get('personas', [PersonController::class, 'index'])->name('people.index');
        Route::post('people', [PersonController::class, 'store'])->name('people.store');
        Route::put('people/{person}', [PersonController::class, 'update'])->name('people.update');
        Route::delete('people/{person}', [PersonController::class, 'destroy'])->name('people.delete');
        Route::get('people', [PersonController::class, 'getPeople'])->name('people.filters.index');

        Route::get('ajustes/metodos-de-pago', [PayMethodController::class, 'index'])->name('busssines.setting.paymethods.index');
        Route::post('paymethods', [PayMethodController::class, 'store'])->name('busssines.setting.paymethods.store');

        Route::get('ajustes/ivas', [IvaController::class, 'index'])->name('busssines.setting.ivas.index');
        Route::post('ivas', [IvaController::class, 'store'])->name('busssines.setting.ivas.store');

        Route::get('ajustes/ices', [IceController::class, 'index'])->name('busssines.setting.ices.index');
        Route::post('ices', [IceController::class, 'store'])->name('busssines.setting.ices.store');

        Route::get('ajustes/retenciones', [WithholdingController::class, 'index'])->name('busssines.setting.withholding.index');
        Route::post('withholdings', [WithholdingController::class, 'store'])->name('busssines.setting.withholding.store');

        Route::get('ajustes/iess', [IessController::class, 'index'])->name('busssines.setting.iess.index');
        Route::post('iess', [IessController::class, 'store'])->name('busssines.setting.iess.store');

        Route::get('ajustes/movimientos-bancarios', [MovementTypeController::class, 'index'])->name('busssines.setting.movementtypes.index');
        Route::post('movementtypes', [MovementTypeController::class, 'store'])->name('busssines.setting.movementtypes.store');

        Route::get('ajustes/gastos', [ExpenseController::class, 'index'])->name('busssines.setting.expenses.index');
        Route::post('expenses', [ExpenseController::class, 'store'])->name('busssines.setting.expenses.store');

        Route::get('ajustes/regimen-pago', [PaymentRegimController::class, 'index'])->name('busssines.setting.paymentregims.index');
        Route::post('paymentRegim', [PaymentRegimController::class, 'store'])->name('busssines.setting.paymentregims.store');

        Route::get('cajas', [BoxController::class, 'index'])->name('boxes.index');
        Route::post('boxes', [BoxController::class, 'store'])->name('boxes.store');
        Route::put('boxes/{box}', [BoxController::class, 'update'])->name('boxes.update');
        Route::delete('boxes/{box}', [BoxController::class, 'destroy'])->name('boxes.delete');
        Route::post('boxes/{box}/open', [BoxController::class, 'open'])->name('boxes.open');
        Route::get('/boxes/{box}/closeinformation', [BoxController::class, 'closeinformation'])->name('boxes.closeinformation');
        Route::post('boxes/{box}/close', [BoxController::class, 'close'])->name('boxes.close');

        //transacciones
        Route::get('movimientos-cajas', [TransactionBoxController::class, 'index'])->name('transaction.boxes.index');
        Route::get('movimientos-cajas/crear', action: [TransactionBoxController::class, 'create'])->name('transaction.boxes.create');
        Route::post('transactionboxes', [TransactionBoxController::class, 'store'])->name('transaction.boxes.store');
    
        Route::get('movimientos-gastos', [TransactionExpenseController::class, 'index'])->name('transaction.expenses.index');
        Route::get('movimientos-gastos/crear', action: [TransactionExpenseController::class, 'create'])->name('transaction.expenses.create');
        Route::post('transactionexpenses', [TransactionExpenseController::class, 'store'])->name('transaction.expenses.store');
    
    
    });
});