<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DnnController;
use App\Http\Controllers\HighwayController;
use App\Http\Controllers\LogController;
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
//         'is_admin' => true,
//         'email' => 'paqrommc@gmail.com',
//         'password' => Hash::make('Paqro2@24!'),
//     ]);
//     return $test;
// });

// Route::get('/alishka2', function () {
//     $user1 = User::where('id', 1)->update([
//         'name' => 'Tamerlan',
//         'is_admin' => true,
//         'email' => 'tamerlan@gmail.com',
//         'password' => Hash::make('PaqroT2@24!'),
//     ]);
//     $user2 = User::create([
//         'name' => 'Jale',
//         'is_admin' => true,
//         'email' => 'jale@gmail.com',
//         'password' => Hash::make('PaqroJ2@24!'),
//     ]);
//     $user3 = User::create([
//         'name' => 'Cavid',
//         'is_admin' => false,
//         'email' => 'cavid@gmail.com',
//         'password' => Hash::make('PaqroC2@24!'),
//     ]);
//     return $user3;
// });

Route::get('/login', [AuthController::class, 'loginView'])->name('login-view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.indexPage');
    Route::get('/visual-table', [DashboardController::class, 'visualTable'])->name('visual-table');
    Route::get('/dashboard/entries', [DashboardController::class, 'entries'])->name('dashboard.entries');
    Route::get('/dashboard/exits', [DashboardController::class, 'exits'])->name('dashboard.exits');
    Route::get('/dashboard/overall', [DashboardController::class, 'overall'])->name('dashboard.overall');

    Route::group(['middleware' => 'isAdmin'], function(){
        Route::get('/dashboardEntry/{entry}/edit', [DashboardController::class, 'entryPage'])->name('dashboard.entries.edit');
        Route::put('/dashboardEntry/{entry}', [DashboardController::class, 'updateEntryPage'])->name('dashboard.entries.update');
        Route::delete('/dashboardEntry/{entry}', [DashboardController::class, 'deleteEntry'])->name('dashboard.entries.delete');
        Route::get('/dashboardExit/{exit}/edit', [DashboardController::class, 'exitPage'])->name('dashboard.exits.edit');
        Route::put('/dashboardExit/{exit}', [DashboardController::class, 'updateExitPage'])->name('dashboard.exits.update');
        Route::delete('/dashboardExit/{exit}', [DashboardController::class, 'deleteExit'])->name('dashboard.exits.delete');
        Route::get('/dashboard/transfer', [DashboardController::class, 'transferPage'])->name('dashboard.transferPage');
        Route::post('/dashboard/transfer', [DashboardController::class, 'transfer'])->name('dashboard.transfer');
        Route::resource('warehouses', WarehouseController::class)->names('warehouses');
        Route::resource('logs', LogController::class)->names('logs');
        Route::get('/highways/{highway}/change', [HighwayController::class, 'changeWarehousePage'])->name('highways.changeWarehousePage');
        Route::put('/highways/{highway}/change', [HighwayController::class, 'changeWarehouse'])->name('highways.changeWarehouse');
    });


    Route::get('/get-products/{warehouseId}', [DashboardController::class, 'getProducts']);

    Route::get('/export-main-excel', [DashboardController::class, 'exportMainProductsExcel'])->name('export.mainExcel');
    Route::get('/export-entry-excel', [DashboardController::class, 'exportEntryProductsExcel'])->name('export.entryExcel');
    Route::get('/export-exit-excel', [DashboardController::class, 'exportExitProductsExcel'])->name('export.exitExcel');
    Route::get('/export-overall-excel', [DashboardController::class, 'exportOverallProductsExcel'])->name('export.overallExcel');

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
// Route::resource('dnns', DnnController::class)->names('dnns');
