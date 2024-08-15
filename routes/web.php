<?php

use App\Http\Controllers\CompanyController;
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

Route::get('/', function () {
    return view('pages.dashboard');
});

Route::get('/login', function () {
    return view('pages.login');
});

Route::resource('companies', CompanyController::class)->names('companies');
Route::resource('categories', SubcategoryController::class)->names('categories');
Route::resource('products', ProductController::class)->names('products');
Route::resource('warehouses', WarehouseController::class)->names('warehouses');
Route::resource('highways', HighwayController::class)->names('highways');