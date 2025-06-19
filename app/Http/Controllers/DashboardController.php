<?php

namespace App\Http\Controllers;

use App\Exports\EntryExport;
use App\Exports\ExitExport;
use App\Exports\MainExport;
use App\Exports\OverallExport;
use App\Models\Company;
use App\Models\Highway;
use App\Models\Log;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Subcategory;
use App\Models\Warehouse;
use App\Models\WarehouseLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class DashboardController extends Controller
{

    public function __construct()
    {
        // Apply middleware only to the create method
        $this->middleware('isAdmin')->only(['create']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $products = Product::all();
        $warehouses = Warehouse::orderBy('id', 'asc')->get();
        $categories = Subcategory::all();

        $query = ProductEntry::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id === "all"
                ? "all"
                : $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }

        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_entries.product_id', $request->product_ids);
        }

        if ($request->category_id) {
            $query->where('product_entries.subcategory_id', $request->category_id);
        }

        if ($request->except_category_ids && count($request->except_category_ids) > 0) {
            $query->whereNotIn('product_entries.subcategory_id', $request->except_category_ids);
        }

        $start = $startDate ?? "2020-01-01 00:00:00";
        $end = $endDate ?? "2030-01-01 00:00:00";

        // Subquery for entries
        $entrySub = DB::table('warehouse_logs')
            ->select([
                'product_id',
                'subcategory_id',
                'to_warehouse_id as warehouse_id',
                DB::raw('SUM(quantity) as entry_total')
            ])
            ->whereBetween('entry_date', [$start, $end])
            ->groupBy('product_id', 'subcategory_id', 'to_warehouse_id');

        // Subquery for exits
        $exitSub = DB::table('warehouse_logs')
            ->select([
                'product_id',
                'subcategory_id',
                'from_warehouse_id as warehouse_id',
                DB::raw('SUM(quantity) as exit_total')
            ])
            ->whereBetween('entry_date', [$start, $end])
            ->groupBy('product_id', 'subcategory_id', 'from_warehouse_id');


        $totalEntries = (clone $query)->select([
                'product_entries.product_id',
                'product_entries.subcategory_id',
                'product_entries.warehouse_id',
                'product_entries.quantity',
                DB::raw('COALESCE(entry.entry_total, 0) as entry_total'),
                DB::raw('COALESCE(exit.exit_total, 0) as exit_total'),
            ])
            ->leftJoinSub($entrySub, 'entry', function ($join) {
                $join->on('entry.product_id', '=', 'product_entries.product_id')
                    ->on('entry.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('entry.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoinSub($exitSub, 'exit', function ($join) {
                $join->on('exit.product_id', '=', 'product_entries.product_id')
                    ->on('exit.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('exit.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->when($warehouseId !== "all", function($query) use ($warehouseId){
                $query->where('product_entries.warehouse_id', $warehouseId);
            });

        $totalQuantities = $totalEntries->sum('quantity');
        $totalEntryCount = $totalEntries->sum('entry_total');
        $totalExitCount = $totalEntries->sum('exit_total');


        $groupedEntries = $query->select([
                'product_entries.product_id',
                'product_entries.measure',
                'product_entries.shelf',
                'product_entries.subcategory_id',
                'product_entries.warehouse_id',
                'w.name as warehouse_name',
                'product_entries.quantity',
                DB::raw('COALESCE(entry.entry_total, 0) as entry_total'),
                DB::raw('COALESCE(exit.exit_total, 0) as exit_total'),
            ])
            ->leftJoinSub($entrySub, 'entry', function ($join) {
                $join->on('entry.product_id', '=', 'product_entries.product_id')
                    ->on('entry.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('entry.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoinSub($exitSub, 'exit', function ($join) {
                $join->on('exit.product_id', '=', 'product_entries.product_id')
                    ->on('exit.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('exit.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'product_entries.warehouse_id')
            ->when($warehouseId !== "all", function($query) use ($warehouseId){
                $query->where('product_entries.warehouse_id', $warehouseId);
            })
            ->orderBy('p.name')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.dashboard.index', [
            'productEntries' => $groupedEntries,
            'totalQuantities' => $totalQuantities,
            'totalEntryCount' => $totalEntryCount,
            'totalExitCount' => $totalExitCount,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_ids' => $request->product_ids ?? null,
            'categories' => $categories,
            'category_id' => $request->category_id ?? null,
            'start_date' => $startDate ?? null,
            'except_category_ids' => $request->except_category_ids ?? null,
            'end_date' => $endDate ?? null
        ]);
    }

    public function entries(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $warehouses = Warehouse::orderBy('id', 'asc')->get();
        $products = Product::all();
        $categories = Subcategory::all();
        $companies = Company::all();

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }

        if ($request->from_warehouse_ids && count($request->from_warehouse_ids) > 0) {
            $query->whereIn('from_warehouse_id', $request->from_warehouse_ids);
        }
        if ($request->except_from_warehouse_ids && count($request->except_from_warehouse_ids) > 0) {
            $query->whereNotIn('from_warehouse_id', $request->except_from_warehouse_ids);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);

            if(count($request->product_ids) == 1 && $request->from_warehouse_ids && count($request->from_warehouse_ids) == 1){
                // Məhsulun ümumi girish sayı
                $query2 = WarehouseLog::query();
                $query2->where(['to_warehouse_id' => $warehouseId, 'product_id' => $request->product_ids[0]]);
                if($request->from_warehouse_ids && count($request->from_warehouse_ids) > 0){
                    $query2->where('from_warehouse_id', $request->from_warehouse_ids);
                }
                if ($request->category_id) {
                    $query2->where('subcategory_id', $request->category_id);
                }
                if ($request->company_id) {
                    $query->where('company_id', $request->company_id);
                }
                if ($startDate && $endDate) {
                    $query2->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
                }
                $query2->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id');
                $totalEntryCount = $query2->sum('warehouse_logs.quantity');
            }
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        // warehouse_logs 
        $warehouseLogs = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                "warehouse_logs.id as id",
                "c.name as company_name",
                "p.name as product_name",
                "p.code as product_code",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "warehouse_logs.measure",
                "warehouse_logs.entry_date as entry_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
            ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
            ->where('warehouse_logs.to_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->paginate(20)->appends($request->query());

        return view('pages.dashboard.entries', [
            'productEntries' => $warehouseLogs,
            'totalEntryCount' => $totalEntryCount ?? 0,
            'products' => $products,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'companies' => $companies,
            'warehouse_id' => $request->warehouse_id ?? null,
            'from_warehouse_ids' => $request->from_warehouse_ids ?? null,
            'except_from_warehouse_ids' => $request->except_from_warehouse_ids ?? null,
            'product_ids' => $request->product_ids ?? null,
            'company_id' => $request->company_id ?? null,
            'category_id' => $request->category_id ?? null,
            'start_date' => $startDate ?? null,
            'end_date' => $endDate ?? null
        ]);
    }

    public function entryPage(WarehouseLog $entry){
        $products = Product::all();
        $warehouses = Warehouse::all();
        if(!$entry->from_warehouse_id){
            $categories = Subcategory::all();
            $companies = Company::all();
    
            $warehouseName = $warehouses->where('id', $entry->to_warehouse_id)->first()?->name;
            $companyName = $companies->where('id', $entry->company_id)->first()?->name;
            $currentProduct = $products->where('id', $entry->product_id)->first();
            $productName = $currentProduct?->name;
            $productCode = $currentProduct?->code;
            $categoryName = $categories->where('id', $entry->subcategory_id)->first()?->name;
            return view('pages.dashboard.entryEdit', compact(
          'entry',
         'products',
                    'warehouses',
                    'warehouseName',
                    'companies',
                    'companyName',
                    'categories',
                    'productName',
                    'productCode',
                    'categoryName',
                ));
        }else{
            return view('pages.dashboard.exitEdit', [
                'exit' => $entry,
                'products' => $products,
                'warehouses' => $warehouses
            ]);
        }
    }

    public function updateEntryPage(Request $request, WarehouseLog $entry){
        try {
            $request->validate([
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'warehouse_name' => 'required|string',
                'company_id' => 'nullable|exists:companies,id',
                'company_name' => 'required|string',
                'product' => 'required|string',
                'product_code' => 'required|string',
                'quantity' => 'required|integer',
                'category' => 'required|string',
                'entry_date' => 'required|date',
            ]);
            // check if warehouse exits else create new one
            if ($request->warehouse_id) {
                $warehouseId = (int)$request->warehouse_id;
            } else {
                $newWarehouse = Warehouse::create(['name' => $request->warehouse_name]);
                $warehouseId = $newWarehouse->id;
            }
            // check if company exits else create new one
            if ($request->company_id) {
                $companyId = (int)$request->company_id;
            } else {
                $newCompany = Company::create(['name' => $request->company_name]);
                $companyId = $newCompany->id;
            }

            $currentCategory = $request->category;
            if ($categoryExits = Subcategory::where(['name' => $currentCategory])->first()) {
                $categoryId = $categoryExits->id;
            } else {
                $newCategory = Subcategory::create(['name' => $currentCategory]);
                $categoryId = $newCategory->id;
            }

            $currentProduct = $request->product;
            $currentProductCode = $request->product_code;
            if ($productExits = Product::where(['name' => $currentProduct, 'code' => $currentProductCode])->first()) {
                $productId = $productExits->id;
            } else {
                $newProduct = Product::create(['name' => $currentProduct, 'code' => $currentProductCode]);
                $productId = $newProduct->id;
            }
            //check if warehouse has current product => if has increase quantity if not create one
            $productEntry = ProductEntry::where(['warehouse_id' => $warehouseId, 'product_id' => $productId, 'subcategory_id' => $categoryId])->first();
            if ($productEntry) {
                // increase
                $productEntry->update(['quantity' => $productEntry->quantity + $request->quantity]);
            } else {
                // create new one
                ProductEntry::create([
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId,
                    'company_id' => $companyId,
                    'quantity' => $request->quantity,
                    'subcategory_id' => $categoryId,
                    'entry_date' => $request->entry_date
                ]);
            }
            // update older one
            $olderProductEntry = ProductEntry::where(['warehouse_id' => $entry->to_warehouse_id, 'product_id' => $entry->product_id, 'subcategory_id' => $entry->subcategory_id])->first();
            $olderProductEntry->update(['quantity' => $olderProductEntry->quantity - $entry->quantity, 'shelf' => $request->shelf]);


            $oldEntryData = [
                'quantity' => $entry->quantity,
                'productInfo' => $entry->product?->name . " (" . $entry->product?->code . ")",
                'to' => $entry->toWarehouse?->name,
                'category' => $entry->subcategory?->name,
                'date' => $entry->entry_date,
            ];

            $entry->update([
                'to_warehouse_id' => $warehouseId,
                'quantity' => $request->quantity,
                'shelf' => $request->shelf,
                'entry_date' => $request->entry_date,
                'product_id' => $productId,
                'subcategory_id' => $categoryId,
                'company_id' => $companyId
            ]);

            $entry->refresh();

            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Düzəliş etdi',
                'model_type' => 'App\Models\WarehouseLog',
                'model_id' => $entry->id,
                'changes' => json_encode(["action" => "Girişə düzəliş etdi", "data" => [
                    "Mehsul" => $oldEntryData["productInfo"] . " ===> " . $entry->product?->name . " (" . $entry->product?->code . ")",
                    "Sayi" => $oldEntryData["quantity"] . " ===> " . $entry->quantity,
                    "Anbara" => $oldEntryData["to"] . " ===> " . ($entry->to_warehouse_id ? $entry->toWarehouse?->name : ""),
                    "Kateqoriya" => $oldEntryData["category"] . " ===> " . $entry->subcategory?->name,
                    "Tarix" => $oldEntryData["date"] . " ===> " . $entry->entry_date,
                    ]], JSON_UNESCAPED_UNICODE)
            ]);

            return redirect()->route('dashboard.index')
                ->with('success', 'Məhsul uğurla dəyişildi.');
        } catch (Throwable $th) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    public function deleteEntry(WarehouseLog $entry){

        $toWarehouse = ProductEntry::where(['warehouse_id' => $entry->to_warehouse_id, 'product_id' => $entry->product_id, 'subcategory_id' => $entry->subcategory_id])->first();
        if($toWarehouse) $toWarehouse->update(['quantity' => $toWarehouse->quantity - $entry->quantity]);
        if($entry->from_warehouse_id){
            $fromWarehouse = ProductEntry::where(['warehouse_id' => $entry->from_warehouse_id, 'product_id' => $entry->product_id, 'subcategory_id' => $entry->subcategory_id])->first();
            if($fromWarehouse) $fromWarehouse->update(['quantity' => $fromWarehouse->quantity + $entry->quantity]);
        }
        Log::create([
            'user_id' => auth()->id(),
            'action' => 'Sildi',
            'model_type' => 'App\Models\WarehouseLog',
            'model_id' => $entry->id,
            'changes' => json_encode(["action" => "Girişi sildi", "data" => [
                "Mehsul" => $entry->product?->name . " (" . $entry->product?->code . ")",
                "Sayi" => $entry->quantity,
                "Anbara" => $entry->toWarehouse?->name,
                "Anbardan" => $entry->from_warehouse_id ? $entry->fromWarehouse?->name : "",
                "Kateqoriya" => $entry->subcategory?->name,
                "Tarix" => $entry->entry_date,
                ]], JSON_UNESCAPED_UNICODE)
        ]);
        $entry->delete();
        return redirect()->back()->with('success', 'Uğurla silindi.');
    }
    public function exits(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $warehouses = Warehouse::orderBy('id', 'asc')->get();
        $products = Product::all();
        $categories = Subcategory::all();

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }

        if ($request->to_warehouse_id) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
            if(count($request->product_ids) == 1){
                // Məhsulun ümumi çıxış sayı
                $query2 = WarehouseLog::query();
                $query2->where(['from_warehouse_id' => $warehouseId, 'product_id' => $request->product_ids[0]]);
                if($request->to_warehouse_id){
                    $query2->where('to_warehouse_id', $request->to_warehouse_id);
                }
                if ($request->category_id) {
                    $query2->where('subcategory_id', $request->category_id);
                }
                if ($request->highway_code) {
                    $query2->where('h.code', $request->highway_code);
                }
                if ($startDate && $endDate) {
                    $query2->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
                }
                $query2->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id');
                $totalExitCount = $query2->sum('warehouse_logs.quantity');
            }
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        // warehouse_logs 
        $warehouseLogs = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                "warehouse_logs.id as id",
                "p.code as product_code",
                "p.name as product_name",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "warehouse_logs.measure",
                "h.code as highway_code",
                // "d.code as dnn_code",
                "warehouse_logs.entry_date as exit_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
            // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
            ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
            ->where('warehouse_logs.from_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->paginate(20)->appends($request->query());

        return view('pages.dashboard.exits', [
            'productExits' => $warehouseLogs,
            'totalExitCount' => $totalExitCount ?? 0,
            'products' => $products,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'warehouse_id' => $request->warehouse_id ?? null,
            'to_warehouse_id' => $request->to_warehouse_id ?? null,
            'product_ids' => $request->product_ids ?? null,
            'highway_code' => $request->highway_code ?? null,
            // 'dnn_code' => $request->dnn_code ?? null,
            'category_id' => $request->category_id ?? null,
            'start_date' => $startDate ?? null,
            'end_date' => $endDate ?? null
        ]);
    }

    public function exitPage(WarehouseLog $exit){
        if($exit->highway_id){
            return redirect()->route('dashboard.index');
        }
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('pages.dashboard.exitEdit', compact(
      'exit',
     'products',
                'warehouses',
            ));
    }

    public function updateExitPage(Request $request, WarehouseLog $exit){
        try {
            $request->validate([
                'from_warehouse' => 'nullable|exists:warehouses,id',
                'to_warehouse' => 'nullable|exists:warehouses,id',
                'product' => 'required|exists:products,id',
                'quantity' => 'required|integer',
                'transfer_date' => 'required|date',
            ]);
            $currentProduct = $request->product;
            $currentQuantity = (float)$request->quantity;
            $currentEntryDate = $request->transfer_date;
            $fromWarehouse = ProductEntry::where(['warehouse_id' => $request->from_warehouse, 'product_id' => $currentProduct])->first();
            if ($fromWarehouse->quantity + $exit->quantity >= $currentQuantity) {
                $fromWarehouse->update(['quantity' => $fromWarehouse->quantity - $currentQuantity]);
                $toWarehouse = ProductEntry::where(['warehouse_id' => $request->to_warehouse, 'product_id' => $currentProduct])->first();
                if ($toWarehouse) {
                    // increase
                    $toWarehouse->update(['quantity' => $toWarehouse->quantity + $currentQuantity]);
                } else {
                    // create
                    ProductEntry::create([
                        'warehouse_id' => $request->to_warehouse,
                        'product_id' => $currentProduct,
                        'company_id' => $fromWarehouse->company_id,
                        'quantity' => $currentQuantity,
                        'subcategory_id' => $fromWarehouse->subcategory_id,
                        'entry_date' => $currentEntryDate
                    ]);
                }
                // decrease older product entry
                $olderToProductEntry = ProductEntry::where(['warehouse_id' => $exit->to_warehouse_id, 'product_id' => $exit->product_id, 'subcategory_id' => $exit->subcategory_id])->first();
                $olderToProductEntry->update(['quantity' => $olderToProductEntry->quantity - $exit->quantity]);

                $olderFromProductEntry = ProductEntry::where(['warehouse_id' => $exit->from_warehouse_id, 'product_id' => $exit->product_id, 'subcategory_id' => $exit->subcategory_id])->first();
                $olderFromProductEntry->update(['quantity' => $olderFromProductEntry->quantity + $exit->quantity]);


                $oldExitData = [
                    'quantity' => $exit->quantity,
                    'productInfo' => $exit->product?->name . " (" . $exit->product?->code . ")",
                    'from' => $exit->fromWarehouse?->name,
                    'to' => $exit->to_warehouse_id ? $exit->toWarehouse?->name : "",
                    'highwayCode' => $exit->highway_id ? $exit->highway?->code : "",
                    'category' => $exit->subcategory?->name,
                    'date' => $exit->entry_date,
                ];

                // update older exit warehouse log
                $exit->update([
                    'from_warehouse_id' => $request->from_warehouse,
                    'to_warehouse_id' => $request->to_warehouse,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->transfer_date,
                    'product_id' => $request->product,
                    'subcategory_id' => $fromWarehouse->subcategory_id,
                    'company_id' => $fromWarehouse->company_id
                ]);

                $exit->refresh();

                Log::create([
                    'user_id' => auth()->id(),
                    'action' => 'Düzəliş etdi',
                    'model_type' => 'App\Models\WarehouseLog',
                    'model_id' => $exit->id,
                    'changes' => json_encode(["action" => "Çıxışa düzəliş etdi", "data" => [
                        "Mehsul" => $oldExitData["productInfo"] . " ===> " . $exit->product?->name . " (" . $exit->product?->code . ")",
                        "Sayi" => $oldExitData["quantity"] . " ===> " . $exit->quantity,
                        "Anbardan" => $oldExitData["from"] . " ===> " . $exit->fromWarehouse?->name,
                        "Anbara" => $oldExitData["to"] ? $oldExitData["to"] . " ===> " . ($exit->to_warehouse_id ? $exit->toWarehouse?->name : "") : "",
                        //"Şassi nömrəsi" => $oldExitData["highwayCode"] ? $oldExitData["highwayCode"] . " ===> " . ($exit->highway_id ? $exit->highway?->code : "") : "",
                        "Kateqoriya" => $oldExitData["category"] . " ===> " . $exit->subcategory?->name,
                        "Tarix" => $oldExitData["date"] . " ===> " . $exit->entry_date,
                        ]], JSON_UNESCAPED_UNICODE)
                ]);

                return redirect()->route('dashboard.index')->with('success', 'Məhsul uğurla dəyişildi.');
            } else {
                return redirect()->route('dashboard.index')
                    ->with('error', 'Anbarda kifayət qədər məhsul yoxdur.');
            }
        } catch (Throwable $th) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    public function deleteExit(WarehouseLog $exit){
        $fromWarehouse = ProductEntry::where(['warehouse_id' => $exit->from_warehouse_id, 'product_id' => $exit->product_id])->first();
        if($fromWarehouse) $fromWarehouse->update(['quantity' => $fromWarehouse->quantity + $exit->quantity]);
        if($exit->to_warehouse_id){
            $toWarehouse = ProductEntry::where(['warehouse_id' => $exit->to_warehouse_id, 'product_id' => $exit->product_id])->first();
            if($toWarehouse) $toWarehouse->update(['quantity' => $toWarehouse->quantity - $exit->quantity]);
        }
        // highway
        if($exit->highway_id){
            $highway = Highway::where('id', $exit->highway_id)->first();
            if($highway) $highway->delete();
        }
        Log::create([
            'user_id' => auth()->id(),
            'action' => 'Sildi',
            'model_type' => 'App\Models\WarehouseLog',
            'model_id' => $exit->id,
            'changes' => json_encode(["action" => "Çıxışı sildi", "data" => [
                "Mehsul" => $exit->product?->name . " (" . $exit->product?->code . ")",
                "Sayi" => $exit->quantity,
                "Anbardan" => $exit->from_warehouse_id ? $exit->fromWarehouse?->name : "",
                "Anbara" => $exit->to_warehouse_id ? $exit->toWarehouse?->name : "",
                "Şassi nömrəsi" => $exit->highway_id ? $exit->highway?->code : "",
                "Kateqoriya" => $exit->subcategory?->name,
                "Tarix" => $exit->entry_date,
                ]], JSON_UNESCAPED_UNICODE)
        ]);
        $exit->delete();
        return redirect()->route('dashboard.index')->with('success', 'Uğurla silindi.');
    }

    public function overall(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $warehouses = Warehouse::all();
        $products = Product::all();
        $categories = Subcategory::all();
        $companies = Company::all();

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
            $warehouse = Warehouse::where('id', $warehouseId)->first();
            $warehouseName = $warehouse->name;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
            $warehouseName = $mainWarehouse->name;
        }

        if($request->to_warehouse_id){
            $query->where('warehouse_logs.to_warehouse_id', $request->to_warehouse_id)->where('warehouse_logs.from_warehouse_id', $warehouseId);
        }else if($request->from_warehouse_id){
            $query->where('warehouse_logs.from_warehouse_id', $request->from_warehouse_id)->where('warehouse_logs.to_warehouse_id', $warehouseId);
        }else{
            if(!$request->type_id){
                $query->where(function($query) use($warehouseId) {
                    $query->where('warehouse_logs.to_warehouse_id', $warehouseId)
                          ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId);
                });
            }else{
                if($request->type_id == 1){
                    $query->where('warehouse_logs.to_warehouse_id', $warehouseId);
                }else{
                    $query->where('warehouse_logs.from_warehouse_id', $warehouseId);
                }
            }
        }

        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        // warehouse_logs 
        $warehouseLogs = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                DB::raw("(SELECT SUM(CASE WHEN wl2.to_warehouse_id = $warehouseId THEN wl2.quantity ELSE -wl2.quantity END) FROM warehouse_logs as wl2 where wl2.product_id = warehouse_logs.product_id and (wl2.to_warehouse_id = $warehouseId or wl2.from_warehouse_id = $warehouseId) and wl2.entry_date < warehouse_logs.entry_date) as residual"),
                "p.name as product_name",
                "p.code as product_code",
                "h.code as highway_code",
                "c.name as company_name",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "warehouse_logs.to_warehouse_id",
                "warehouse_logs.entry_date as entry_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
            ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
            ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
            // ->where(function($query) use($warehouseId) {
            //     $query->where('warehouse_logs.to_warehouse_id', $warehouseId)
            //           ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId);
            // })
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->paginate(20)->appends($request->query());

        return view('pages.dashboard.overall', [
            'productEntries' => $warehouseLogs,
            'warehouseName' => $warehouseName,
            'warehouseId' => $warehouseId,
            'products' => $products,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'companies' => $companies,
            'highway_code' => $request->highway_code ?? null,
            'warehouse_id' => $request->warehouse_id ?? null,
            'to_warehouse_id' => $request->to_warehouse_id ?? null,
            'from_warehouse_id' => $request->from_warehouse_id ?? null,
            'product_ids' => $request->product_ids ?? null,
            'type_id' => $request->type_id ?? null,
            'company_id' => $request->company_id ?? null,
            'category_id' => $request->category_id ?? null,
            'start_date' => $startDate ?? null,
            'end_date' => $endDate ?? null
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $warehouses = Warehouse::all();
        $companies = Company::all();
        $categories = Subcategory::all();

        $mainWarehouse = Warehouse::getMainWarehouse();

        return view('pages.dashboard.create', [
            'products' => $products,
            'warehouses' => $warehouses,
            'companies' => $companies,
            'categories' => $categories,
            'mainWarehouse' => $mainWarehouse
        ]);
    }

    public function getProducts($warehouseId)
    {
        $products = ProductEntry::select(
            "product_entries.id as id",
            "p.id as product_id",
            "p.name as product_name",
            "s.name as category_name",
            "p.code as product_code",
            "product_entries.quantity"
        )
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
            ->where('product_entries.warehouse_id', $warehouseId)
            ->where('product_entries.quantity', '>', 0)
            ->get();

        return response()->json($products);
    }

    public function transferPage()
    {
        $warehouses = Warehouse::all();
        $mainWarehouse = Warehouse::getMainWarehouse();
        $warehouseId = $mainWarehouse->id;
        return view('pages.dashboard.transfer', [
            'warehouses' => $warehouses,
            'mainWarehouseId' => $warehouseId
        ]);
    }

    public function transfer(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'from_warehouse' => 'required|exists:warehouses,id',
                'to_warehouse' => 'required|exists:warehouses,id',

                'products' => 'required|array|min:1',
                'products.*' => 'nullable',

                'quantities' => 'required|array|min:1',
                'quantities.*' => 'nullable|numeric',

                'notes' => 'required|array|min:1',
                'notes.*' => 'nullable|string',

                'transfer_dates' => 'required|array|min:1',
                'transfer_dates.*' => 'required|date',
            ]);

            // loop through all arrays 
            $loopLength = count($request->products);
            $productNames = [];
            $productCodes = [];
            $categories = [];
            for ($i = 0; $i < $loopLength; $i++) {
                if(!($request->products[$i] && $request->quantities[$i] && $request->notes[$i])){
                    $productNames[] = '';
                    $productCodes[] = '';
                    $categories[] = '';
                    continue;
                }
                $currentProductEntry = $request->products[$i];
                $currentQuantity = (float)$request->quantities[$i];
                $currentMeasure = $request->notes[$i];
                $currentEntryDate = $request->transfer_dates[$i];
                $fromWarehouse = ProductEntry::where('id', $currentProductEntry)->first();
                if ($fromWarehouse->quantity >= $currentQuantity) {
                    $currentProductData = Product::where('id', $fromWarehouse->product_id)->first();
                    $productNames[] = $currentProductData->name ?? '';
                    $productCodes[] = $currentProductData->code ?? '';
                    $currentProductCategory = Subcategory::where('id', $fromWarehouse->subcategory_id)->first();
                    $categories[] = $currentProductCategory->name ?? '';

                    $fromWarehouse->update(['quantity' => $fromWarehouse->quantity - $currentQuantity]);
                    $toWarehouse = ProductEntry::where(['warehouse_id' => $request->to_warehouse, 'product_id' => $fromWarehouse->product_id, 'subcategory_id' => $fromWarehouse->subcategory_id])->first();
                    if ($toWarehouse) {
                        // increase
                        $toWarehouse->update(['quantity' => $toWarehouse->quantity + $currentQuantity]);
                    } else {
                        // create
                        ProductEntry::create([
                            'warehouse_id' => $request->to_warehouse,
                            'product_id' => $fromWarehouse->product_id,
                            'company_id' => $fromWarehouse->company_id,
                            'quantity' => $currentQuantity,
                            'measure' => $currentMeasure,
                            'subcategory_id' => $fromWarehouse->subcategory_id,
                            'entry_date' => $currentEntryDate
                        ]);
                    }
                    $wrhsLogId = WarehouseLog::create([
                        'from_warehouse_id' => $request->from_warehouse,
                        'to_warehouse_id' => $request->to_warehouse,
                        'quantity' => $currentQuantity,
                        'measure' => $currentMeasure,
                        'entry_date' => $currentEntryDate,
                        'product_id' => $fromWarehouse->product_id,
                        'subcategory_id' => $fromWarehouse->subcategory_id,
                        'company_id' => $fromWarehouse->company_id
                    ]);
                    Log::create([
                        'user_id' => auth()->id(),
                        'action' => 'Yaratdı',
                        'model_type' => 'App\Models\WarehouseLog',
                        'model_id' => $wrhsLogId->id,
                        'changes' => json_encode(["action" => "Yeni çıxış daxil etdi", "data" => [
                            "Say" => $currentQuantity,
                            "Mehsul" => $wrhsLogId->product?->name . " (" . $wrhsLogId->product?->code . ")",
                            "Anbardan" => $wrhsLogId->fromWarehouse?->name,
                            "Anbara" => $wrhsLogId->toWarehouse?->name,
                            "Kateqoriya" => $wrhsLogId->subcategory?->name,
                            "Tarix" => $currentEntryDate
                            ]], JSON_UNESCAPED_UNICODE)
                    ]);
                } else {
                    DB::rollBack();
                    return redirect()->route('dashboard.index')
                        ->with('error', 'Anbarda kifayət qədər məhsul yoxdur.');
                }
            }
            DB::commit();
            if($request->action == 'print'){
                // export pdf
                $path = public_path('images/signature.png');
                $imageData = base64_encode(file_get_contents($path));
                $imageType = pathinfo($path, PATHINFO_EXTENSION);
                $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;
                $toWarehouseman = Warehouse::where('id', $request->to_warehouse)->first();
                $data = [
                    'warehouseName' => $toWarehouseman->name,
                    'to' => $request->to_whom ?? $toWarehouseman->name . ' filialının Anbardarı ' . $toWarehouseman->warehouseman,
                    'quantities' => $request->quantities,
                    'pdfDocNumber' => $request->pdf_doc_number,
                    'transfer_date' => Carbon::parse($request->pdf_date)->format('d.m.Y'),
                    'products' => $productNames,
                    'codes' => $productCodes,
                    'categories' => $categories,
                    'notes' => $request->notes,
                    'base64Image' => $base64Image
                ];
                $pdf = Pdf::loadView('pages.pdf.warehouse_exit', $data)->setPaper('a4')
                ->setOptions(['isRemoteEnabled' => true]);
                $fileName = 'warehouse_exit_' . now()->format('Y-m-d_H-i-s') . '.pdf';
                Storage::put("public/{$fileName}", $pdf->output());
                // $pdf->save($fileName);
                
                return view('pages.pdf.download', ['fileName' => $fileName]);
            }else{
                return redirect()->route('dashboard.index')
                    ->with('success', 'Uğurlu.');
            }
        } catch (Throwable $th) {
            DB::rollBack();
            dd($th);
            return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'warehouse_name' => 'required|string',

                'company_id' => 'nullable|exists:companies,id',
                'company_name' => 'required|string',

                'products' => 'required|array|min:1',
                'products.*' => 'nullable|string',

                'product_codes' => 'required|array|min:1',
                'product_codes.*' => 'nullable|string',

                'quantities' => 'required|array|min:1',
                'quantities.*' => 'nullable|numeric',

                'categories' => 'required|array|min:1',
                'categories.*' => 'nullable|string',

                'notes' => 'required|array|min:1',
                'notes.*' => 'nullable|string',

                'shelfs' => 'required|array|min:1',
                'shelfs.*' => 'nullable|string',

                'entry_dates' => 'required|array|min:1',
                'entry_dates.*' => 'required|date',
            ]);
            if(!($request->products[0] && $request->quantities[0] && $request->categories[0] && $request->notes[0] && $request->shelfs[0])){
                return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
            }
            // check if warehouse exits else create new one
            if ($request->warehouse_id) {
                $warehouseId = (int)$request->warehouse_id;
            } else {
                $newWarehouse = Warehouse::create(['name' => $request->warehouse_name]);
                $warehouseId = $newWarehouse->id;
            }
            // check if company exits else create new one
            if ($request->company_id) {
                $companyId = (int)$request->company_id;
            } else {
                $newCompany = Company::create(['name' => $request->company_name]);
                $companyId = $newCompany->id;
            }

            // loop through all arrays 
            $loopLength = count($request->products);
            for ($i = 0; $i < $loopLength; $i++) {
                if(!($request->products[$i] && $request->quantities[$i] && $request->categories[$i] && $request->notes[$i] && $request->shelfs[$i])){
                    continue;
                }
                // check if category exits else create new one
                $currentCategory = $request->categories[$i];
                if ($categoryExits = Subcategory::where(['name' => $currentCategory])->first()) {
                    $categoryId = $categoryExits->id;
                } else {
                    $newCategory = Subcategory::create(['name' => $currentCategory]);
                    $categoryId = $newCategory->id;
                }
                // check if product exits else create new one with new product code
                $currentProductWithCode = explode('---', $request->products[$i]);
                $currentProduct = $currentProductWithCode[0];
                $currentProductCode = $request->product_codes[$i];
                if ($productExits = Product::where(['name' => $currentProduct, 'code' => $currentProductCode])->first()) {
                    $productId = $productExits->id;
                    // if product exits check if warehouse has current product => if has increase quantity if not create one
                } else {
                    $newProduct = Product::create(['name' => $currentProduct, 'code' => $currentProductCode]);
                    $productId = $newProduct->id;
                }
                //check if warehouse has current product => if has increase quantity if not create one
                $productEntry = ProductEntry::where(['warehouse_id' => $warehouseId, 'product_id' => $productId, 'subcategory_id' => $categoryId])->first();
                if ($productEntry) {
                    // increase
                    $productEntry->update(['quantity' => $productEntry->quantity + $request->quantities[$i]]);
                } else {
                    // create
                    ProductEntry::create([
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                        'company_id' => $companyId,
                        'quantity' => $request->quantities[$i],
                        'measure' => $request->notes[$i],
                        'shelf' => $request->shelfs[$i],
                        'subcategory_id' => $categoryId,
                        'entry_date' => $request->entry_dates[$i]
                    ]);
                }
                $wrhsLogId = WarehouseLog::create([
                    'to_warehouse_id' => $warehouseId,
                    'quantity' => $request->quantities[$i],
                    'measure' => $request->notes[$i],
                    'shelf' => $request->shelfs[$i],
                    'entry_date' => $request->entry_dates[$i],
                    'product_id' => $productId,
                    'subcategory_id' => $categoryId,
                    'company_id' => $companyId
                ]);
                Log::create([
                    'user_id' => auth()->id(),
                    'action' => 'Yaratdı',
                    'model_type' => 'App\Models\WarehouseLog',
                    'model_id' => $wrhsLogId->id,
                    'changes' => json_encode(["action" => "Yeni giriş daxil etdi", "data" => [
                        "Say" => $request->quantities[$i],
                        "Mehsul" => $currentProduct . " (" . $currentProductCode . ")",
                        "Anbara" => $wrhsLogId->toWarehouse?->name,
                        "Kateqoriya" => $wrhsLogId->subcategory?->name,
                        "Tarix" => $request->entry_dates[$i]
                        ]], JSON_UNESCAPED_UNICODE)
                ]);
            }
            return redirect()->route('dashboard.index')
                ->with('success', 'Məhsul uğurla əlavə edildi.');
        } catch (Throwable $th) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    public function exportMainProducts(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }
        $query = ProductEntry::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id === "all"
                ? "all"
                : $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }

        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_entries.product_id', $request->product_ids);
        }

        if ($request->category_id) {
            $query->where('product_entries.subcategory_id', $request->category_id);
        }

        if ($request->except_category_ids && count($request->except_category_ids) > 0) {
            $query->whereNotIn('product_entries.subcategory_id', $request->except_category_ids);
        }

        $start = $startDate ?? "2020-01-01 00:00:00";
        $end = $endDate ?? "2030-01-01 00:00:00";

        // Subquery for entries
        $entrySub = DB::table('warehouse_logs')
            ->select([
                'product_id',
                'subcategory_id',
                'to_warehouse_id as warehouse_id',
                DB::raw('SUM(quantity) as entry_total')
            ])
            ->whereBetween('entry_date', [$start, $end])
            ->groupBy('product_id', 'subcategory_id', 'to_warehouse_id');

        // Subquery for exits
        $exitSub = DB::table('warehouse_logs')
            ->select([
                'product_id',
                'subcategory_id',
                'from_warehouse_id as warehouse_id',
                DB::raw('SUM(quantity) as exit_total')
            ])
            ->whereBetween('entry_date', [$start, $end])
            ->groupBy('product_id', 'subcategory_id', 'from_warehouse_id');

        $products = $query->select([
                'product_entries.product_id',
                'product_entries.measure',
                'product_entries.shelf',
                'product_entries.subcategory_id',
                'product_entries.warehouse_id',
                'w.name as warehouse_name',
                'product_entries.quantity',
                's.name as subcategory_name',
                DB::raw('COALESCE(entry.entry_total, 0) as entry_total'),
                DB::raw('COALESCE(exit.exit_total, 0) as exit_total'),
            ])
            ->leftJoinSub($entrySub, 'entry', function ($join) {
                $join->on('entry.product_id', '=', 'product_entries.product_id')
                    ->on('entry.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('entry.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoinSub($exitSub, 'exit', function ($join) {
                $join->on('exit.product_id', '=', 'product_entries.product_id')
                    ->on('exit.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('exit.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'product_entries.warehouse_id')
            ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
            ->when($warehouseId !== "all", function($query) use ($warehouseId){
                $query->where('product_entries.warehouse_id', $warehouseId);
            })
            ->orderBy('p.name')
            ->get();

        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data = [
                'products' => $products->items() // Extract items if paginated
            ];
        } else {
            $data = [
                'products' => $products // Directly use collection if not paginated
            ];
        }
        // Load a view and pass the products data to it
        $pdf = Pdf::loadView('pages.pdf.main', $data)->setPaper('a4')
            ->setOptions(['defaultFont' => 'DejaVu Sans']);

        // Return the generated PDF
        return $pdf->download('products.pdf');
    }

    public function exportEntryProducts(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }
        if ($request->from_warehouse_ids && count($request->from_warehouse_ids) > 0) {
            $query->whereIn('from_warehouse_id', $request->from_warehouse_ids);
        }
        if ($request->except_from_warehouse_ids && count($request->except_from_warehouse_ids) > 0) {
            $query->whereNotIn('from_warehouse_id', $request->except_from_warehouse_ids);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        if ($request->get('export_type') === 'all') {
            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "c.name as company_name",
                    "p.name as product_name",
                    "p.code as product_code",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
                ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
                ->where('warehouse_logs.to_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 20;
            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "c.name as company_name",
                    "p.name as product_name",
                    "p.code as product_code",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
                ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
                ->where('warehouse_logs.to_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        }
        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data = [
                'products' => $products->items() // Extract items if paginated
            ];
        } else {
            $data = [
                'products' => $products // Directly use collection if not paginated
            ];
        }
        // Load a view and pass the products data to it
        $pdf = Pdf::loadView('pages.pdf.entries', $data)->setPaper('a4')
            ->setOptions(['defaultFont' => 'DejaVu Sans']);

        // Return the generated PDF
        return $pdf->download('products.pdf');
    }

    public function exportExitProducts(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }
        if ($request->to_warehouse_id) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        if ($request->get('export_type') === 'all') {
            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "p.code as product_code",
                    "p.name as product_name",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "h.code as highway_code",
                    "warehouse_logs.entry_date as exit_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->where('warehouse_logs.from_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 20;

            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "p.code as product_code",
                    "p.name as product_name",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "h.code as highway_code",
                    "warehouse_logs.entry_date as exit_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->where('warehouse_logs.from_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        }
        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data = [
                'products' => $products->items() // Extract items if paginated
            ];
        } else {
            $data = [
                'products' => $products // Directly use collection if not paginated
            ];
        }
        // Load a view and pass the products data to it
        $pdf = Pdf::loadView('pages.pdf.exits', $data)->setPaper('a4')
            ->setOptions(['defaultFont' => 'DejaVu Sans']);

        // Return the generated PDF
        return $pdf->download('products.pdf');
    }

    public function exportOverallProducts(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }
        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
            $warehouse = Warehouse::where('id', $warehouseId)->first();
            $warehouseName = $warehouse->name;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
            $warehouseName = $mainWarehouse->name;
        }

        if($request->to_warehouse_id){
            $query->where('warehouse_logs.to_warehouse_id', $request->to_warehouse_id)->where('warehouse_logs.from_warehouse_id', $warehouseId);
        }else if($request->from_warehouse_id){
            $query->where('warehouse_logs.from_warehouse_id', $request->from_warehouse_id)->where('warehouse_logs.to_warehouse_id', $warehouseId);
        }else{
            if(!$request->type_id){
                $query->where(function($query) use($warehouseId) {
                    $query->where('warehouse_logs.to_warehouse_id', $warehouseId)
                          ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId);
                });
            }else{
                if($request->type_id == 1){
                    $query->where('warehouse_logs.to_warehouse_id', $warehouseId);
                }else{
                    $query->where('warehouse_logs.from_warehouse_id', $warehouseId);
                }
            }
        }

        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }


        if ($request->get('export_type') === 'all') {
            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    DB::raw("(SELECT SUM(CASE WHEN wl2.to_warehouse_id = $warehouseId THEN wl2.quantity ELSE -wl2.quantity END) FROM warehouse_logs as wl2 where wl2.product_id = warehouse_logs.product_id and (wl2.to_warehouse_id = $warehouseId or wl2.from_warehouse_id = $warehouseId) and wl2.entry_date < warehouse_logs.entry_date) as residual"),
                    "p.name as product_name",
                    "p.code as product_code",
                    "h.code as highway_code",
                    "c.name as company_name",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.to_warehouse_id",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 20;

            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    DB::raw("(SELECT SUM(CASE WHEN wl2.to_warehouse_id = $warehouseId THEN wl2.quantity ELSE -wl2.quantity END) FROM warehouse_logs as wl2 where wl2.product_id = warehouse_logs.product_id and (wl2.to_warehouse_id = $warehouseId or wl2.from_warehouse_id = $warehouseId) and wl2.entry_date < warehouse_logs.entry_date) as residual"),
                    "p.name as product_name",
                    "p.code as product_code",
                    "h.code as highway_code",
                    "c.name as company_name",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.to_warehouse_id",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        }
        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data = [
                'products' => $products->items(),
                'warehouseName' => $warehouseName,
                'warehouseId' => $warehouseId,
            ];
        } else {
            $data = [
                'products' => $products,
                'warehouseName' => $warehouseName,
                'warehouseId' => $warehouseId,
            ];
        }
        // Load a view and pass the products data to it
        $pdf = Pdf::loadView('pages.pdf.overall', $data)->setPaper('a4')
            ->setOptions(['defaultFont' => 'DejaVu Sans']);

        // Return the generated PDF
        return $pdf->download('products.pdf');
    }

    public function exportMainProductsExcel(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }
        $query = ProductEntry::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id === "all"
                ? "all"
                : $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }

        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_entries.product_id', $request->product_ids);
        }

        if ($request->category_id) {
            $query->where('product_entries.subcategory_id', $request->category_id);
        }

        if ($request->except_category_ids && count($request->except_category_ids) > 0) {
            $query->whereNotIn('product_entries.subcategory_id', $request->except_category_ids);
        }

        $start = $startDate ?? "2020-01-01 00:00:00";
        $end = $endDate ?? "2030-01-01 00:00:00";

        $entrySub = DB::table('warehouse_logs')
            ->select([
                'product_id',
                'subcategory_id',
                'to_warehouse_id as warehouse_id',
                DB::raw('SUM(quantity) as entry_total')
            ])
            ->whereBetween('entry_date', [$start, $end])
            ->groupBy('product_id', 'subcategory_id', 'to_warehouse_id');

        $exitSub = DB::table('warehouse_logs')
            ->select([
                'product_id',
                'subcategory_id',
                'from_warehouse_id as warehouse_id',
                DB::raw('SUM(quantity) as exit_total')
            ])
            ->whereBetween('entry_date', [$start, $end])
            ->groupBy('product_id', 'subcategory_id', 'from_warehouse_id');

        $products = $query->select([
                'product_entries.measure',
                'product_entries.shelf',
                'w.name as warehouse_name',
                'product_entries.quantity',
                's.name as subcategory_name',
                'p.name as product_name',
                'p.code as product_code',
                DB::raw('COALESCE(entry.entry_total, 0) as entry_total'),
                DB::raw('COALESCE(exit.exit_total, 0) as exit_total'),
            ])
            ->leftJoinSub($entrySub, 'entry', function ($join) {
                $join->on('entry.product_id', '=', 'product_entries.product_id')
                    ->on('entry.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('entry.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoinSub($exitSub, 'exit', function ($join) {
                $join->on('exit.product_id', '=', 'product_entries.product_id')
                    ->on('exit.subcategory_id', '=', 'product_entries.subcategory_id')
                    ->on('exit.warehouse_id', '=', 'product_entries.warehouse_id');
            })
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'product_entries.warehouse_id')
            ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
            ->when($warehouseId !== "all", function($query) use ($warehouseId){
                $query->where('product_entries.warehouse_id', $warehouseId);
            })
            ->orderBy('p.name')
            ->get();
        return Excel::download(new MainExport($products), 'anbar_qaliq.xlsx');
    }

    public function exportEntryProductsExcel(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }
        if ($request->from_warehouse_ids && count($request->from_warehouse_ids) > 0) {
            $query->whereIn('from_warehouse_id', $request->from_warehouse_ids);
        }
        if ($request->except_from_warehouse_ids && count($request->except_from_warehouse_ids) > 0) {
            $query->whereNotIn('from_warehouse_id', $request->except_from_warehouse_ids);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        $products = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                "c.name as company_name",
                "p.name as product_name",
                "p.code as product_code",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "warehouse_logs.entry_date as entry_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
            ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
            ->where('warehouse_logs.to_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->get();
        
        return Excel::download(new EntryExport($products), 'anbar_girisler.xlsx');
    }

    public function exportExitProductsExcel(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }
        if ($request->to_warehouse_id) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }

        $products = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                "p.code as product_code",
                "p.name as product_name",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "h.code as highway_code",
                "warehouse_logs.entry_date as exit_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
            ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
            ->where('warehouse_logs.from_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->get();
        
        return Excel::download(new ExitExport($products), 'anbar_cixislar.xlsx');
    }

    public function exportOverallProductsExcel(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
            $warehouse = Warehouse::where('id', $warehouseId)->first();
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }

        if($request->to_warehouse_id){
            $query->where('warehouse_logs.to_warehouse_id', $request->to_warehouse_id)->where('warehouse_logs.from_warehouse_id', $warehouseId);
        }else if($request->from_warehouse_id){
            $query->where('warehouse_logs.from_warehouse_id', $request->from_warehouse_id)->where('warehouse_logs.to_warehouse_id', $warehouseId);
        }else{
            if(!$request->type_id){
                $query->where(function($query) use($warehouseId) {
                    $query->where('warehouse_logs.to_warehouse_id', $warehouseId)
                          ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId);
                });
            }else{
                if($request->type_id == 1){
                    $query->where('warehouse_logs.to_warehouse_id', $warehouseId);
                }else{
                    $query->where('warehouse_logs.from_warehouse_id', $warehouseId);
                }
            }
        }

        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('warehouse_logs.entry_date', [$startDate, $endDate]);
        }
        
        $products = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                DB::raw("(SELECT SUM(CASE WHEN wl2.to_warehouse_id = $warehouseId THEN wl2.quantity ELSE -wl2.quantity END) FROM warehouse_logs as wl2 where wl2.product_id = warehouse_logs.product_id and (wl2.to_warehouse_id = $warehouseId or wl2.from_warehouse_id = $warehouseId) and wl2.entry_date < warehouse_logs.entry_date) as residual"),
                "p.name as product_name",
                "p.code as product_code",
                "h.code as highway_code",
                "c.name as company_name",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "warehouse_logs.to_warehouse_id",
                "warehouse_logs.entry_date as entry_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
            ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
            ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->get();
        
        return Excel::download(new OverallExport($products, $warehouseId), 'anbar_umumi.xlsx');
    }

    public function visualTable()
    {

        $products = Product::all();
        $warehouses = Warehouse::with('product_entries')->get();

        return view('pages.visual_table', [
            'products' => $products,
            'warehouses' => $warehouses
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
