<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\HistoryController;
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
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\DipController;
use App\Http\Controllers\WetStockController;
use App\Http\Controllers\AccountHistoryController;

// Dashboard at root, protected
Route::get('/', [AdminController::class, 'dashboard'])->middleware('admin')->name('admin.dashboard');

// Auth routes (redirect to dashboard if already logged in)
Route::get('/login', function(){
    return view('pages.login');
})->middleware('login')->name('admin.login');
Route::post('/login', [AdminController::class, 'login'])->middleware('login')->name('admin.login.post');

// Backwards compatibility: /admin -> /
Route::get('/admin', function(){
    return redirect()->route('admin.dashboard');
})->middleware('admin');


Route::middleware('admin')->group(function () {
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    /*************************Customers_Routes***************************/
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::post('/customers/update', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/delete/{id}', [CustomerController::class, 'delete'])->name('admin.customers.delete');

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

    /*************************Roles_Permissions_Routes***************************/
    Route::middleware(['admin', 'superadmin'])->group(function () {
        Route::get('/roles-permissions', [App\Http\Controllers\Management\RolePermissionController::class, 'index'])->name('admin.roles-permissions.index');
        Route::post('/roles/store', [App\Http\Controllers\Management\RolePermissionController::class, 'storeRole'])->name('admin.roles.store');
        Route::post('/roles/update', [App\Http\Controllers\Management\RolePermissionController::class, 'updateRole'])->name('admin.roles.update');
        Route::delete('/roles/delete/{id}', [App\Http\Controllers\Management\RolePermissionController::class, 'deleteRole'])->name('admin.roles.delete');
        Route::post('/roles/assign', [App\Http\Controllers\Management\RolePermissionController::class, 'assignRoleToUser'])->name('admin.roles.assign');
        Route::get('/roles/{id}/permissions', [App\Http\Controllers\Management\RolePermissionController::class, 'getRolePermissions'])->name('admin.roles.permissions');
    });

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
    Route::post('/purchases/delete', [PurchaseController::class, 'delete'])->name('purchase.delete');

    /*************************Sales_Routes***************************/
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales/delete', [SalesController::class, 'delete'])->name('sales.delete');
    Route::post('/product/tank/update', [SalesController::class, 'productTankUpdate'])->name('sales.product.tank.update');

    // Nozzle Sales
    Route::get('/sales/nozzle', [App\Http\Controllers\NozzleSalesController::class, 'index'])->name('sales.nozzle.index');
    Route::post('/sales/nozzle/precheck', [App\Http\Controllers\NozzleSalesController::class, 'precheck'])->name('sales.nozzle.precheck');
    Route::post('/sales/nozzle/product-nozzles', [App\Http\Controllers\NozzleSalesController::class, 'productNozzles'])->name('sales.nozzle.product_nozzles');
    Route::post('/sales/nozzle/store', [App\Http\Controllers\NozzleSalesController::class, 'store'])->name('sales.nozzle.store');

    // Lubricant (General) Sales
    Route::get('/sales/lubricant', [App\Http\Controllers\LubricantSalesController::class, 'index'])->name('sales.lubricant.index');
    Route::post('/sales/lubricant/store', [App\Http\Controllers\LubricantSalesController::class, 'store'])->name('sales.lubricant.store');

        /*************************Daybook_Routes***************************/
    Route::get('/daybook', [App\Http\Controllers\DaybookController::class, 'index'])->name('admin.daybook.index');

    /*************************Payments_Routes***************************/
    // Bank Receiving Routes
    Route::get('/payments/bank-receiving', [App\Http\Controllers\PaymentController::class, 'bankReceiving'])->name('admin.payments.bank-receiving');
    Route::post('/payments/bank-receiving/store', [App\Http\Controllers\PaymentController::class, 'storeBankReceiving'])->name('admin.payments.bank-receiving.store');

    // Bank Payments Routes
    Route::get('/payments/bank-payments', [App\Http\Controllers\PaymentController::class, 'bankPayments'])->name('admin.payments.bank-payments');
    Route::post('/payments/bank-payments/store', [App\Http\Controllers\PaymentController::class, 'storeBankPayment'])->name('admin.payments.bank-payments.store');

    // Cash Receiving Routes
    Route::get('/payments/cash-receiving', [App\Http\Controllers\PaymentController::class, 'cashReceiving'])->name('admin.payments.cash-receiving');
    Route::post('/payments/cash-receiving/store', [App\Http\Controllers\PaymentController::class, 'storeCashReceiving'])->name('admin.payments.cash-receiving.store');

    // Cash Payments Routes
    Route::get('/payments/cash-payments', [App\Http\Controllers\PaymentController::class, 'cashPayments'])->name('admin.payments.cash-payments');
    Route::post('/payments/cash-payments/store', [App\Http\Controllers\PaymentController::class, 'storeCashPayment'])->name('admin.payments.cash-payments.store');

    // Delete Transaction Route
    Route::delete('/payments/transaction/delete', [App\Http\Controllers\PaymentController::class, 'deleteTransaction'])->name('admin.payments.transaction.delete');

    /*************************History_Routes***************************/
    Route::get('/history/purchases', [HistoryController::class, 'purchases'])->name('admin.history.purchases');
    Route::get('/history/sales', [HistoryController::class, 'sales'])->name('admin.history.sales');
    Route::get('/history/bank-receivings', [HistoryController::class, 'bankReceivings'])->name('admin.history.bank-receivings');
    Route::get('/history/bank-payments', [HistoryController::class, 'bankPayments'])->name('admin.history.bank-payments');
    Route::get('/history/cash-receipts', [HistoryController::class, 'cashReceipts'])->name('admin.history.cash-receipts');
    Route::get('/history/cash-payments', [HistoryController::class, 'cashPayments'])->name('admin.history.cash-payments');
    Route::get('/history/journal-vouchers', [HistoryController::class, 'journalVouchers'])->name('admin.history.journal-vouchers');

    /*************************Journal_Voucher_Routes***************************/
    Route::get('/journal', [JournalController::class, 'index'])->name('admin.journal.index');
    Route::post('/journal/store', [JournalController::class, 'store'])->name('admin.journal.store');
    Route::delete('/journal/delete/{id}', [JournalController::class, 'destroy'])->name('admin.journal.destroy');
    Route::get('/journal/vendors', [JournalController::class, 'getVendorsByType'])->name('admin.journal.vendors');

    /*************************Trial_Balance_Routes***************************/
    Route::get('/trial-balance', [TrialBalanceController::class, 'index'])->name('admin.trial-balance.index');
    Route::get('/trial-balance/export', [TrialBalanceController::class, 'export'])->name('admin.trial-balance.export');

    /*************************Profit_Routes***************************/
    Route::get('/profit', [ProfitController::class, 'index'])->name('admin.profit.index');

    /*************************Dips_Routes***************************/
    Route::get('/dips', [DipController::class, 'index'])->name('admin.dips.index');
    Route::post('/dips/store', [DipController::class, 'store'])->name('admin.dips.store');
    Route::post('/dips/get-liters', [DipController::class, 'getDipLiters'])->name('admin.dips.get-liters');
    Route::post('/dips/get-tank-product', [DipController::class, 'getTankProduct'])->name('admin.dips.get-tank-product');
    Route::delete('/dips/delete', [DipController::class, 'destroy'])->name('admin.dips.delete');

    /*************************Wet_Stock_Routes***************************/
    Route::get('/wet-stock', [WetStockController::class, 'index'])->name('admin.wet-stock.index');
    Route::get('/wet-stock/export', [WetStockController::class, 'export'])->name('admin.wet-stock.export');

    /*************************Ledger_Routes***************************/
    Route::get('/ledger/product', [LedgerController::class, 'productLedger'])->name('admin.ledger.product');
    Route::get('/ledger/supplier', [LedgerController::class, 'supplierLedger'])->name('admin.ledger.supplier');
    Route::get('/ledger/customer', [LedgerController::class, 'customerLedger'])->name('admin.ledger.customer');
    Route::get('/ledger/bank', [LedgerController::class, 'bankLedger'])->name('admin.ledger.bank');
    Route::get('/ledger/cash', [LedgerController::class, 'cashLedger'])->name('admin.ledger.cash');
    Route::get('/ledger/mp', [LedgerController::class, 'mpLedger'])->name('admin.ledger.mp');
    Route::get('/ledger/expense', [LedgerController::class, 'expenseLedger'])->name('admin.ledger.expense');
    Route::get('/ledger/income', [LedgerController::class, 'incomeLedger'])->name('admin.ledger.income');
    Route::get('/ledger/employee', [LedgerController::class, 'employeeLedger'])->name('admin.ledger.employee');

    /*************************Reports_Routes***************************/
    Route::get('/reports/account-history', [AccountHistoryController::class, 'index'])->name('admin.reports.account-history');
    Route::get('/reports/all-stocks', [App\Http\Controllers\ReportsController::class, 'allStocks'])->name('admin.reports.all-stocks');
    Route::get('/reports/summary', [App\Http\Controllers\ReportsController::class, 'summary'])->name('admin.reports.summary');

    Route::get('/reports/purchase-transport', [App\Http\Controllers\ReportsController::class, 'purchaseTransportReport'])->name('admin.reports.purchase-transport');
    Route::match(['get', 'post'], '/reports/sale-transport', [App\Http\Controllers\ReportsController::class, 'saleTransportReport'])->name('admin.reports.sale-transport');
    Route::post('/reports/chamber-data', [App\Http\Controllers\ReportsController::class, 'getChamberData'])->name('admin.reports.chamber-data');
});

Route::name('admin.management.')->group(function () {
    Route::get('/settings', [App\Http\Controllers\Management\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [App\Http\Controllers\Management\SettingsController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth'])->group(function () {
    // Logs Routes
    Route::get('/logs', [LogsController::class, 'index'])->name('admin.logs.index');
});
