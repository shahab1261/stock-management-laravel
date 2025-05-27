<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Management\BankController;
use App\Http\Controllers\Management\TankController;
use App\Http\Controllers\Management\UserController;
use App\Http\Controllers\Management\DriverController;
use App\Http\Controllers\Management\IncomeController;
use App\Http\Controllers\Management\NozzleController;
use App\Http\Controllers\Management\ExpenseController;
use App\Http\Controllers\Management\ProductController;
use App\Http\Controllers\Management\CustomerController;
use App\Http\Controllers\Management\EmployeeController;
use App\Http\Controllers\Management\SettingsController;
use App\Http\Controllers\Management\SupplierController;
use App\Http\Controllers\Management\TankLariController;
use App\Http\Controllers\Management\TerminalController;
use App\Http\Controllers\Management\TransportController;

Route::get('/', function(){
    return view('pages.login');
})->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');


Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

    /*************************Customers_Routes***************************/
    Route::get('customers/', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::post('customers/store', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::post('customers/update', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('customers/delete/{id}', [CustomerController::class, 'delete'])->name('admin.customers.delete');

    /*************************Banks_Routes***************************/
    Route::get('/banks', [BankController::class, 'index'])->name('admin.banks.index');
    Route::post('/banks/store', [BankController::class, 'store'])->name('admin.banks.store');
    Route::post('/banks/update', [BankController::class, 'update'])->name('admin.banks.update');
    Route::delete('/banks/delete/{id}', [BankController::class, 'delete'])->name('admin.banks.delete');

    /*************************Settings_Routes***************************/
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('admin.settings.password');

    /*************************Profile_Routes***************************/
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('admin.profile.password');

    /*************************Tank_Lari_Routes***************************/
    Route::get('/tanklari', [TankLariController::class, 'index'])->name('admin.tanklari.index');
    Route::post('/tanklari/store', [TankLariController::class, 'store'])->name('admin.tanklari.store');
    Route::post('/tanklari/update', [TankLariController::class, 'update'])->name('admin.tanklari.update');
    Route::get('/tanklari/delete/{id}', [TankLariController::class, 'delete'])->name('admin.tanklari.delete');

    /*************************Drivers_Routes***************************/
    Route::get('/drivers', [DriverController::class, 'index'])->name('admin.drivers.index');
    Route::post('/drivers/store', [DriverController::class, 'store'])->name('admin.drivers.store');
    Route::post('/drivers/update', [DriverController::class, 'update'])->name('admin.drivers.update');
    Route::delete('/drivers/delete/{id}', [DriverController::class, 'delete'])->name('admin.drivers.delete');

    /*************************Expenses_Routes***************************/
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::post('/expenses/update', [ExpenseController::class, 'update'])->name('admin.expenses.update');
    Route::delete('/expenses/delete/{id}', [ExpenseController::class, 'delete'])->name('admin.expenses.delete');

    /*************************Incomes_Routes***************************/
    Route::get('/incomes', [IncomeController::class, 'index'])->name('admin.incomes.index');
    Route::post('/incomes/store', [IncomeController::class, 'store'])->name('admin.incomes.store');
    Route::post('/incomes/update', [IncomeController::class, 'update'])->name('admin.incomes.update');
    Route::delete('/incomes/delete/{id}', [IncomeController::class, 'delete'])->name('admin.incomes.delete');

    /*************************Nozzles_Routes***************************/
    Route::get('/nozzles', [NozzleController::class, 'index'])->name('admin.nozzles.index');
    Route::get('/products/{productId}/tank', [NozzleController::class, 'getTankByProduct'])->name('admin.nozzles.getTankByProduct');
    Route::post('/nozzles/store', [NozzleController::class, 'store'])->name('admin.nozzles.store');
    Route::post('/nozzles/update', [NozzleController::class, 'update'])->name('admin.nozzles.update');
    Route::delete('/nozzles/delete/{id}', [NozzleController::class, 'destroy'])->name('admin.nozzles.delete');

    /*************************Suppliers_Routes***************************/
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('admin.suppliers.index');
    Route::post('/suppliers/store', [SupplierController::class, 'store'])->name('admin.suppliers.store');
    Route::post('/suppliers/update', [SupplierController::class, 'update'])->name('admin.suppliers.update');
    Route::get('/suppliers/delete/{id}', [SupplierController::class, 'delete'])->name('admin.suppliers.delete');

    /*************************Users_Routes***************************/
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users/store', [UserController::class, 'store'])->name('admin.users.store');
    Route::post('/users/update', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('/users/delete/{id}', [UserController::class, 'delete'])->name('admin.users.delete');

    /*************************Terminals_Routes***************************/
    Route::get('/terminals', [TerminalController::class, 'index'])->name('admin.terminals.index');
    Route::post('/terminals/store', [TerminalController::class, 'store'])->name('admin.terminals.store');
    Route::post('/terminals/update', [TerminalController::class, 'update'])->name('admin.terminals.update');
    Route::delete('/terminals/delete/{id}', [TerminalController::class, 'delete'])->name('admin.terminals.delete');

    /*************************Employees_Routes***************************/
    Route::get('/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::post('/employees/store', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::post('/employees/update', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('/employees/delete/{id}', [EmployeeController::class, 'delete'])->name('admin.employees.delete');

    /*************************Transports_Routes***************************/
    Route::get('/transports', [TransportController::class, 'index'])->name('admin.transports.index');
    Route::post('/transports/store', [TransportController::class, 'store'])->name('admin.transports.store');
    Route::post('/transports/update', [TransportController::class, 'update'])->name('admin.transports.update');
    Route::delete('/transports/delete/{id}', [TransportController::class, 'delete'])->name('admin.transports.delete');

    /*************************Products_Routes***************************/
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products/store', [ProductController::class, 'store'])->name('admin.products.store');
    Route::post('/products/update', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.delete');

    /*************************Tanks_Routes***************************/
    Route::get('/tanks', [TankController::class, 'index'])->name('admin.tanks.index');
    Route::post('/tanks/store', [TankController::class, 'store'])->name('admin.tanks.store');
    Route::post('/tanks/update', [TankController::class, 'update'])->name('admin.tanks.update');
    Route::delete('/tanks/delete/{id}', [TankController::class, 'delete'])->name('admin.tanks.delete');
    Route::get('/tanks/{id}/dip-charts', [TankController::class, 'viewDipCharts'])->name('admin.tanks.dip_charts');
    Route::get('/tanks/{id}/dip-charts-page', [TankController::class, 'dipChartsIndex'])->name('admin.tanks.dip_charts.index');

    /*************************Purchase_Routes***************************/
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('product/tank/update', [PurchaseController::class, 'productTankUpdate'])->name('product.tank.update');
    Route::post('product/rate/update', [PurchaseController::class, 'productRateUpdate'])->name('product.rate.update');
    Route::post('tank/chamber/data', [PurchaseController::class, 'tankChamberData'])->name('tank.chamber.data');
    Route::post('purchase/chamber/data', [PurchaseController::class, 'getChamberData'])->name('purchase.chamber.data');
    Route::delete('/purchases/delete', [PurchaseController::class, 'delete'])->name('purchase.delete');

    /*************************Purchase_Routes***************************/
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales/store', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::delete('/sales/delete', [SalesController::class, 'delete'])->name('sales.delete');
});

Route::prefix('admin')->name('admin.management.')->group(function () {
    Route::get('/settings', [App\Http\Controllers\Management\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [App\Http\Controllers\Management\SettingsController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth'])->group(function () {
    // Logs Routes
    Route::get('/admin/logs', [LogsController::class, 'index'])->name('admin.logs.index');
});
