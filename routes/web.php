<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DnnController;
use App\Http\Controllers\HighwayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\WarehouseController;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

// Route::get('/alishka', function () {
//     // create login controller
//     $test = Warehouse::create([
//         'name' => 'Şirvan anbarı',
//         'is_main' => true
//     ]);
//     $user = User::create([
//         'name' => 'Şirvan',
//         'email' => 'paqrommc@gmail.com',
//         'password' => Hash::make('Paqro2@24!'),
//     ]);
//     return $test;
// });
Route::get('/login', [AuthController::class, 'loginView'])->name('login-view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::group(['middleware' => 'auth'], function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.indexPage');
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
    Route::get('/download-pdf', function (Request $request) {
        $fileName = $request->query('file');
        $filePath = "public/{$fileName}";
        // Trigger the download if the file exists
        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }
    
        abort(404);
    })->name('download.pdf');
    Route::resource('dashboard', DashboardController::class)->names('dashboard');
    Route::resource('highways', HighwayController::class)->names('highways');
});


//Route::resource('companies', CompanyController::class)->names('companies');
// Route::resource('categories', SubcategoryController::class)->names('categories');
// Route::resource('products', ProductController::class)->names('products');
Route::resource('warehouses', WarehouseController::class)->names('warehouses');
// Route::resource('dnns', DnnController::class)->names('dnns');
