<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HighwayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

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

Route::get('/login', function () {
    return view('pages.login');
});

Route::get('/dashboard/entries', [DashboardController::class, 'entries'])->name('dashboard.entries');
Route::get('/dashboard/exits', [DashboardController::class, 'exits'])->name('dashboard.exits');
Route::get('/dashboard/transfer', [DashboardController::class, 'transferPage'])->name('dashboard.transferPage');
Route::post('/dashboard/transfer', [DashboardController::class, 'transfer'])->name('dashboard.transfer');
Route::get('/get-products/{warehouseId}', [DashboardController::class, 'getProducts']);

Route::resource('dashboard', DashboardController::class)->names('dashboard');
Route::resource('companies', CompanyController::class)->names('companies');
Route::resource('categories', SubcategoryController::class)->names('categories');
Route::resource('products', ProductController::class)->names('products');
Route::resource('warehouses', WarehouseController::class)->names('warehouses');
Route::resource('highways', HighwayController::class)->names('highways');
