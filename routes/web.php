<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\DnnController;
use App\Http\Controllers\HighwayController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\LimitController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Models\ProductEntry;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseLog;
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

Route::get('/login', [AuthController::class, 'loginView'])->name('login-view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.indexPage');
    Route::get('/visual-table', [DashboardController::class, 'visualTable'])->name('visual-table');
    Route::get('/dashboard/entries', [DashboardController::class, 'entries'])->name('dashboard.entries');
    Route::get('/dashboard/exits', [DashboardController::class, 'exits'])->name('dashboard.exits');
    Route::get('/dashboard/overall', [DashboardController::class, 'overall'])->name('dashboard.overall');
    Route::resource('warehouses', WarehouseController::class)->names('warehouses');
    Route::resource('users', UserController::class)->names('users');
    Route::resource('products', ProductController::class)->names('products');
    Route::get('/limits', [LimitController::class, 'index'])->name('limit.index');

    Route::group(['middleware' => 'isAdmin'], function(){
        Route::get('/dashboardEntry/{entry}/edit', [DashboardController::class, 'entryPage'])->name('dashboard.entries.edit');
        Route::put('/dashboardEntry/{entry}', [DashboardController::class, 'updateEntryPage'])->name('dashboard.entries.update');
        Route::delete('/dashboardEntry/{entry}', [DashboardController::class, 'deleteEntry'])->name('dashboard.entries.delete');
        Route::get('/dashboardExit/{exit}/edit', [DashboardController::class, 'exitPage'])->name('dashboard.exits.edit');
        Route::put('/dashboardExit/{exit}', [DashboardController::class, 'updateExitPage'])->name('dashboard.exits.update');
        Route::delete('/dashboardExit/{exit}', [DashboardController::class, 'deleteExit'])->name('dashboard.exits.delete');
        Route::get('/dashboard/transfer', [DashboardController::class, 'transferPage'])->name('dashboard.transferPage');
        Route::post('/dashboard/transfer', [DashboardController::class, 'transfer'])->name('dashboard.transfer');
        Route::patch('users/deactivate/{user}', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::patch('users/activate/{user}', [UserController::class, 'activate'])->name('users.activate');
        Route::resource('logs', LogController::class)->names('logs');
        Route::get('/highways/{highway}/change', [HighwayController::class, 'changeWarehousePage'])->name('highways.changeWarehousePage');
        Route::put('/highways/{highway}/change', [HighwayController::class, 'changeWarehouse'])->name('highways.changeWarehouse');
        Route::get('import-excel', [ImportExcelController::class, 'create'])->name('import-excel-page');
        Route::post('import-excel', [ImportExcelController::class, 'store'])->name('import-excel');
        Route::post('/backup-database', [DatabaseBackupController::class, 'backupDatabase'])->name('backup.database');
        Route::get('/limits/{entry}', [LimitController::class, 'edit'])->name('limit.edit');
        Route::put('/limits/{entry}', [LimitController::class, 'update'])->name('limit.update');
        Route::get('/product-categories', [SubcategoryController::class, 'listProductCategories'])->name('listProductCategories');
        Route::post('/update-product-category', [SubcategoryController::class, 'updateCategory'])->name('updateProductCategory');
    });


    Route::get('/get-products/{warehouseId}', [DashboardController::class, 'getProducts']);
    Route::get('/get-products-by-query/{warehouseId}', [DashboardController::class, 'getProductsByQuery']);
    Route::get('/get-product-highway-data/{productEntryId}/{highwayCode}', [DashboardController::class, 'getProductHighwayData'])->where('highwayCode', '.*');
    
    Route::get('/export-main-excel', [DashboardController::class, 'exportMainProductsExcel'])->name('export.mainExcel');
    Route::get('/export-limit-excel', [LimitController::class, 'exportLimitProductsExcel'])->name('export.limitExcel');
    Route::get('/export-entry-excel', [DashboardController::class, 'exportEntryProductsExcel'])->name('export.entryExcel');
    Route::get('/export-exit-excel', [DashboardController::class, 'exportExitProductsExcel'])->name('export.exitExcel');
    Route::get('/export-overall-excel', [DashboardController::class, 'exportOverallProductsExcel'])->name('export.overallExcel');
    Route::get('/export-highway-excel/{highwayId}', [HighwayController::class, 'exportHighwayExcel'])->name('export.highwayExcel');

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
// Route::resource('dnns', DnnController::class)->names('dnns');
