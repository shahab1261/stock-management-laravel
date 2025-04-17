<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Management\BankController;
use App\Http\Controllers\Management\CustomerController;
use App\Http\Controllers\Management\TankLariController;

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

    /*************************Tank_Lari_Routes***************************/
    Route::get('/tanklari', [TankLariController::class, 'index'])->name('admin.tanklari.index');
    Route::post('/tanklari/store', [TankLariController::class, 'store'])->name('admin.tanklari.store');
    Route::post('/tanklari/update', [TankLariController::class, 'update'])->name('admin.tanklari.update');
    Route::get('/tanklari/delete/{id}', [TankLariController::class, 'delete'])->name('admin.tanklari.delete');

});
