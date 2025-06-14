<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\DnnController;
use App\Http\Controllers\HighwayController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Models\Product;
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

// Route::get('/alishka', function () {
//     // create login controller
//     $user = User::create([
//         'name' => 'Şirvan',
//         'is_admin' => true,
//         'email' => 'paqrommc@gmail.com',
//         'password' => Hash::make('Paqro2@24!'),
//     ]);
//     $test = Warehouse::create([
//         'name' => 'Şirvan anbarı',
//         'is_main' => true
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

// Route::get('/deleteProducts', function(){
//     $categoryName = 'MTZ 1221';
//     $category = Subcategory::where('name', $categoryName)->first();
//     if($category){
//         $productEntries = ProductEntry::where('subcategory_id', $category->id)->delete();
//         $warehouseLogs = WarehouseLog::where('subcategory_id', $category->id)->delete();
//     }
// });

Route::get('/login', [AuthController::class, 'loginView'])->name('login-view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/smthsmth', function(){
//     $data = [
//         [null, '24 lük şlanq', '1', '1221K'],
//         [null, '30x62x16,25 RULMAN (6206)', '2', 'Kult'],
//         [null, '3150 Y Freze düz dişli Z 35 M7001003', '1', 'Kult'],
//         [null, '3150 Y Freze düz dişli ZB M7001002', '1', 'Kult'],
//         [null, '40x90x23 Rulman (32206)', '3', 'Kult'],
//         [null, '420/70R24 1221 qabaq təkər', '6', 'T/Y'],
//         [null, '50x90x32 Rulman', '4', 'Kult'],
//         [null, 'Akb civata diş boyu 22 M14x1,5x50 özel civata', '27', 'Kult'],
//         [null, 'Akkumlyator 12V 100AH', '6', '80K'],
//         ['80-4605040', 'Arıtma qol sağ (TYAQA PRAVAYA)', '1', '80K'],
//         ['80-4605040-01', 'Artırma qol sol (TYAQA LEVAYA)', '1', '80K'],
//         [null, 'Atval kotan (ünlü) layder', '6', 'Kult'],
//         [null, 'Bolt qayka 19-luq (ədəd kotan)', '100', 'Bolt'],
//         [null, 'Çöp doğrayan kardanı kristavinası Təzə GU2300C', '2', 'Kult'],
//         [null, 'Çöp doğrayan kardanı Təzə GU2050C', '3', 'Kult'],
//         [null, 'Dəmir təkər.', '2', 'Kult'],
//         [null, 'DİSK LAQONDA (115LİK )       ƏDƏDİN', '25', 'Təc'],
//         ['85-1601120-Б', 'DİSK OPORNIY', '3', '1221K'],
//         [null, 'Elektrod 2-lik Magmaweld', '2', 'Təc'],
//         [null, 'Ftulka starter dəst', '18', '80K'],
//         [null, 'Gəvahin 12-lik.', '20', 'Kult'],
//         [null, 'Gəvahin altlığı qısa.', '8', 'Kult'],
//         [null, 'Gəvahin altlığı uzun.', '13', 'Kult'],
//         ['240-1000108-90', 'giliz dəsti d/b 240 (2 yağlı) 38mm mmz (eksport)', '1', '80K'],
//         ['245-1002021-A1У-10', 'Giliz (QİLZA BLOKA SİLİNDROV)', '12', '80K'],
//         ['DİFA 4318+4318-01', 'Hava filtiri (ELEMENT FİL.OCİSTKİ VOZDUXA)', '10', '1221K'],
//         [null, 'Hidravlik şlank 19-luq 1221', '2', '1221K'],
//         ['1221-4625010', 'HİDROSİLİNDR', '3', '1221K'],
//         [null, 'Kapron dinamo(Dinomanın ehtiyat hissəsi)', '5', '80K'],
//         ['72-2203025PH', 'Kardanın kristavinası', '4', '1221K'],
//         ['260-1004062', 'Kolso Kompressionnoe', '12', '1221K'],
//         ['260-1004063', 'Kolso Kompressionnoe', '12', '1221K'],
//         ['Д260-1005100-ЕН1', 'KOM.Vkladış korennoy', '2', '1221K'],
//         ['Д260-1004140-ЕН1', 'KOM.Vkladış şatunnıy', '2', '1221K'],
//         [null, 'Kotan korpusu.', '1', 'Kult'],
//         [null, 'Kotan qoşqusu Zabrat', '1', 'Kult'],
//         ['260-1003108', 'Krişkanın prokladkası (PROKLADKA KRIŞKİ)', '8', '1221K'],
//         [null, 'Layder 12-lik.', '1', 'Kult'],
//         [null, 'Layder 14-lük', '4', 'Kult'],
//         [null, 'litol24 tomoil 16kq', '1', 'T/Y'],
//         [null, 'magru rutile d.4,0 (4,85 kq) A (elektrod)', '12', 'Təc'],
//         ['1.2-110x135-1', 'MANJETA.', '2', '1221K'],
//         ['2.2-65x90-3', 'MANJETA..', '2', '1221K'],
//         [null, 'Megru metal kəsmə 230*25 (345641)', '25', 'Təc'],
//         [null, 'Nal dinamo (Dinomanın ehtiyat hissəsi)', '5', '80K'],
//         [null, 'nasos (yanacaq üçün)', '1', 'Təc'],
//         ['6205', 'Paçevnik', '1', 'komb'],
//         ['6205', 'Paçevnik', '1', 'komb'],
//         [null, 'Pataynı m10x70', '10', 'Bolt'],
//         ['70-4605026', 'Prisepin valı (OS)', '1', '80K'],
//         ['D-260 PROK.DİZELYA', 'Prokladka dəst (REMKOMPLEKT PROKLADOK D-260)', '5', '1221K'],
//         ['142-1601314-Б', 'Prujina', '3', '1221K'],
//         [null, 'Purjun requliyator', '5', '80K'],
//         [null, 'qayka m10', '7', 'Bolt'],
//         [null, 'Qayka m18 (m18x150) YENİ', '4', 'Bolt'],
//         ['50-4605012-01', 'RASKOS', '2', '80K'],
//         ['172,1112110-1201 d 245', 'raspidetel 172,01 mtz-89-12/21 raspititel zavodskoy  farsunka ucluqu', '12', '1221K'],
//         [null, 'Ratorlu mala bıçağı (sağ, sol)', '60', 'Kult'],
//         [null, 'Ratorlu mala kardanı kristavinası Təzə GU2300C', '4', 'Kult'],
//         [null, 'Rele starter', '4', '80K'],
//         ['А-1250', 'REMEN', '3', '80K'],
//         [null, 'REMEN12,5-1250 PRASTOY', '1', '80K'],
//         ['A-1157', 'REMEN A-1157', '5', '1221K'],
//         ['A-1220', 'REMEN A-1220', '5', '1221K'],
//         [null, 'Rulman UCF210 Yataklı (2000 dev/dk Çalışmaktadır) Yeni', '4', 'Kult'],
//         ['d-240-1007020', 'salnik klapan (göy) mtz 82-89 1-kt', '9', '80K'],
//         ['1220-4605107', 'Sırğa (SERQA   )', '2', '1221K'],
//         ['1220-4605168Б', 'Sırğa (SERQA )', '2', '1221K'],
//         ['142-1303010', 'ŞLANQ...', '3', '1221K'],
//         [null, 'Şlanq 24 2 metr', '3', '80K'],
//         [null, 'Şlanq 24 əyri 1 metr', '1', '80K'],
//         [null, 'Şlanq 24 əyri 2 metr', '5', '80K'],
//         [null, 'Şlanq 32  1m', '4', '80K'],
//         [null, 'Şlanq PQS 220 SM', '1', '80K'],
//         [null, 'TECH TOMOİL 20/50 CH-4 200LİTR BOCKA', '200', 'T/Y'],
//         [null, 'Tomoil Antifriz (qırmızı)', '120', 'T/Y'],
//         [null, 'Tomoil Arow ISO MQE 46 200 (bocka)', '200', 'T/Y'],
//         [null, 'TOMOİL Engine 15W-40 CH-4/SJ 200L', '200', 'T/Y'],
//         [null, 'Tomoil M10DM 200L (boçka)', '400', 'T/Y'],
//         ['100-4635300', 'TROS (Morse L-900mm)', '3', '1221K'],
//         [null, 'Üst krişkanın praklatkası 80x', '2', '80K'],
//         [null, 'V kayış XPB-1550 (kauçuk) Yeni Çöp doğrayan', '5', 'Kult'],
//         ['A23.01-78-260', 'VKLADIŞ 12/21 H1 ŞATUNIY D-260H1', '3', '1221K'],
//         [null, 'Vom kasasının şestrnası zbor (təmir olunmuş)', '1', 'Təmir'],
//         [null, 'Vom lenta yatağı (təmir olunmuş)', '1', 'Təmir'],
//         [null, 'Vom (Təmir olunmuş)', '1', 'Təmir'],
//         ['D-260FM035-101 9,2,22', 'YAĞ FİLTRİ 12/21', '12', '1221K'],
//         ['d-260-1011020', 'yağ nasosu 12/21 rus maslınıy nasos', '1', '1221K'],
//         [null, 'Yanacaq filtiri', '12', '1221K'],
//         ['1220-4605610-01', 'Yan qol ruçkalı MTZ (1221 raskos )', '1', '1221K'],
//         ['32313-TUR', 'Yastıq (80x qabaq təkər padşevnik)', '4', '80K'],
//         ['952-4607140-04', 'Yüksək təzyiqli şlanq (RVD) (952-4607140-04)', '2', '1221K'],
//         ['74023708600 БАТЭ', 'Бендикс стартера - Starterin bendeksi', '4', '80K'],
//         ['112-2203010', 'Вал карданный', '1', '1221K'],
//         ['Д-260 260-1005015-В-04', 'Вал коленчатый 1221 kalenvalı', '1', '1221K'],
//         ['70-4202044-Б', 'Вал коронной шестерни (Val 8 şilisli)', '1', '80K'],
//         ['А61.11.001', 'Вилка Qoşqu üçün', '1', '80K'],
//         ['50-1002022', 'Кольцо (салник гилза)', '2', '80K'],
//         ['50-1002022', 'Кольцо (салник гилза)', '4', 'Sub'],
//         ['100х125-12 FKM', 'Манжета 100х125-12 FKM', '1', '80K'],
//         ['240-1004060-А1', 'Моторокомплект п/колец (на 4 цил.) Мотордеталь (Kolça)', '3', '80K'],
//         ['50-1003020-02-03', 'Прокладка головки блока цилиндров 50-1003020-02-03', '1', '80K'],
//         ['S24х0360 D-10.1 SN 0-90', 'РВД 680-4607140', '2', '80K'],
//         ['S24х0600 D-10.1SN 0-90', 'РВД 680-4607140-04', '2', '80K'],
//         ['S30х0410 D-16.2SN 0-90', 'РВД 952-4607140-09', '2', '80K'],
//         ['А6109002', 'СЕРГА (styajkanın sırğası)', '4', '80K'],
//         ['1220-4605120', 'Стяжка', '2', '1221K'],
//         ['70-4605050', 'Тяга', '1', '80K'],
//         ['70-4605055', 'Тяга', '1', '80K'],
//         ['1220-4605590', 'Тяга МТЭ верхняя (bel tyaqası)', '3', '1221K'],
//         ['85-1601130', 'Фередо 1221 (Беларусия)', '6', '80K'],
//         ['80- 4202019-Б-01', 'Хвостовик (с 6 шлицами)', '2', '80K'],
//         [null, 'Щетка стартера - Starterin şotkası', 12, '80K'],
//     ];

//     for ($i=0; $i < count($data); $i++) { 
//         $code = $data[$i][0];
//         $name = $data[$i][1];
//         $quantity = (int)$data[$i][2];
//         $category = $data[$i][3];
        
//         $wrongs = [];

//         $product = Product::where(['code' => $code, 'name' => $name])->first();

//         if($product){
//             $productEntry = ProductEntry::where(['product_id' => $product->id])->first();
//             if($productEntry){
//                 die($productEntry);
//             }
//         }else{
//             $wrongs[] = ['code' => $code, 'name' => $name];
//         }

//     }
// });

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
        Route::resource('users', UserController::class)->names('users');
        Route::patch('users/deactivate/{user}', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::patch('users/activate/{user}', [UserController::class, 'activate'])->name('users.activate');
        Route::resource('products', ProductController::class)->names('products');
        Route::resource('logs', LogController::class)->names('logs');
        Route::get('/highways/{highway}/change', [HighwayController::class, 'changeWarehousePage'])->name('highways.changeWarehousePage');
        Route::put('/highways/{highway}/change', [HighwayController::class, 'changeWarehouse'])->name('highways.changeWarehouse');
        Route::get('import-excel', [ImportExcelController::class, 'create'])->name('import-excel-page');
        Route::post('import-excel', [ImportExcelController::class, 'store'])->name('import-excel');
        Route::post('/backup-database', [DatabaseBackupController::class, 'backupDatabase'])->name('backup.database');
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
// Route::resource('dnns', DnnController::class)->names('dnns');
