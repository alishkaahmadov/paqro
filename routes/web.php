<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DnnController;
use App\Http\Controllers\HighwayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.index');
    } else {
        return view('pages.login');
        // return redirect()->route('login');
    }
});

Route::get('/login', function () {
    // create login controller
    return view('pages.login');
});

Route::get('/visual-table', [DashboardController::class, 'visualTable'])->name('visual-table');
Route::get('/dashboard/overall', [DashboardController::class, 'overall'])->name('dashboard.overall');
Route::get('/dashboard/entries', [DashboardController::class, 'entries'])->name('dashboard.entries');
Route::get('/dashboard/exits', [DashboardController::class, 'exits'])->name('dashboard.exits');
Route::get('/dashboard/transfer', [DashboardController::class, 'transferPage'])->name('dashboard.transferPage');
Route::post('/dashboard/transfer', [DashboardController::class, 'transfer'])->name('dashboard.transfer');
Route::get('/get-products/{warehouseId}', [DashboardController::class, 'getProducts']);
Route::get('/export-main-products', [DashboardController::class, 'exportMainProducts'])->name('export.mainProducts');
Route::get('/export-entry-products', [DashboardController::class, 'exportEntryProducts'])->name('export.entryProducts');
Route::get('/export-exit-products', [DashboardController::class, 'exportExitProducts'])->name('export.exitProducts');
Route::get('/export-overall-products', [DashboardController::class, 'exportOverallProducts'])->name('export.overallProducts');

Route::resource('dashboard', DashboardController::class)->names('dashboard');
Route::resource('companies', CompanyController::class)->names('companies');
Route::resource('categories', SubcategoryController::class)->names('categories');
Route::resource('products', ProductController::class)->names('products');
Route::resource('warehouses', WarehouseController::class)->names('warehouses');
Route::resource('highways', HighwayController::class)->names('highways');
Route::resource('dnns', DnnController::class)->names('dnns');
