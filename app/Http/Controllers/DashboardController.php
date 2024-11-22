<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Highway;
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
use Throwable;

class DashboardController extends Controller
{
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
        $warehouses = Warehouse::all();
        $categories = Subcategory::all();

        $query = ProductEntry::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
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

        $groupedEntries = $query->select(
                    DB::raw('(select sum(warehouse_logs.quantity) from warehouse_logs where warehouse_logs.to_warehouse_id = ? and product_id = product_entries.product_id and entry_date between ? and ? group by warehouse_logs.to_warehouse_id) as entry_total'),
                    DB::raw('(select sum(warehouse_logs.quantity) from warehouse_logs where warehouse_logs.from_warehouse_id = ? and product_id = product_entries.product_id and entry_date between ? and ? group by warehouse_logs.from_warehouse_id) as exit_total'),
                    'product_entries.product_id',
                    'product_entries.subcategory_id', 
                    'product_entries.warehouse_id', 
                    'product_entries.quantity')
            ->addBinding([$warehouseId, $startDate ?? "2020-01-01 00:00:00", $endDate ?? "2030-01-01 00:00:00", $warehouseId, $startDate ?? "2020-01-01 00:00:00", $endDate ?? "2030-01-01 00:00:00"], 'select')
            ->where('product_entries.warehouse_id', $warehouseId)
            ->where('product_entries.quantity', '>', 0)
            ->get();
        return view('pages.dashboard.index', [
            'productEntries' => $groupedEntries,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_ids' => $request->product_ids ?? null,
            'categories' => $categories,
            'category_id' => $request->category_id ?? null,
            'start_date' => $startDate ?? null,
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

        $warehouses = Warehouse::all();
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

        if ($request->from_warehouse_id) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }
        if ($request->product_ids && count($request->product_ids) > 0) {
            $query->whereIn('product_id', $request->product_ids);

            if(count($request->product_ids) == 1){
                // Məhsulun ümumi girish sayı
                $query2 = WarehouseLog::query();
                $query2->where(['to_warehouse_id' => $warehouseId, 'product_id' => $request->product_ids[0]]);
                if($request->from_warehouse_id){
                    $query2->where('from_warehouse_id', $request->from_warehouse_id);
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
            'from_warehouse_id' => $request->from_warehouse_id ?? null,
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
            $olderProductEntry->update(['quantity' => $olderProductEntry->quantity - $entry->quantity]);
            $entry->update([
                'to_warehouse_id' => $warehouseId,
                'quantity' => $request->quantity,
                'entry_date' => $request->entry_date,
                'product_id' => $productId,
                'subcategory_id' => $categoryId,
                'company_id' => $companyId
            ]);
            return redirect()->route('dashboard.index')
                ->with('success', 'Məhsul uğurla dəyişildi.');
        } catch (Throwable $th) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    public function deleteEntry(WarehouseLog $entry){
        $toWarehouse = ProductEntry::where(['warehouse_id' => $entry->to_warehouse_id, 'product_id' => $entry->product_id])->first();
        if($toWarehouse) $toWarehouse->update(['quantity' => $toWarehouse->quantity - $entry->quantity]);
        if($entry->from_warehouse_id){
            $fromWarehouse = ProductEntry::where(['warehouse_id' => $entry->from_warehouse_id, 'product_id' => $entry->product_id])->first();
            if($fromWarehouse) $fromWarehouse->update(['quantity' => $fromWarehouse->quantity + $entry->quantity]);
        }
        $entry->delete();
        return redirect()->route('dashboard.index')->with('success', 'Uğurla silindi.');
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

        $warehouses = Warehouse::all();
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
            $currentQuantity = (int)$request->quantity;
            $currentEntryDate = $request->transfer_date;
            $fromWarehouse = ProductEntry::where(['warehouse_id' => $request->from_warehouse, 'product_id' => $currentProduct])->first();
            if ($fromWarehouse->quantity >= $currentQuantity) {
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

        return view('pages.dashboard.create', [
            'products' => $products,
            'warehouses' => $warehouses,
            'companies' => $companies,
            'categories' => $categories
        ]);
    }

    public function getProducts($warehouseId)
    {
        $products = ProductEntry::select(
            "p.id as product_id",
            "p.name as product_name",
            "s.name as category_name",
            "p.code as product_code",
            "product_entries.quantity"
        )
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
            ->where('product_entries.warehouse_id', $warehouseId)
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
                'quantities.*' => 'nullable|integer',

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
                $currentProduct = $request->products[$i];
                $currentQuantity = (int)$request->quantities[$i];
                $currentEntryDate = $request->transfer_dates[$i];
                $fromWarehouse = ProductEntry::where(['warehouse_id' => $request->from_warehouse, 'product_id' => $currentProduct])->first();
                if ($fromWarehouse->quantity >= $currentQuantity) {
                    $currentProductData = Product::where('id', $currentProduct)->first();
                    $productNames[] = $currentProductData->name ?? '';
                    $productCodes[] = $currentProductData->code ?? '';
                    $currentProductCategory = Subcategory::where('id', $fromWarehouse->subcategory_id)->first();
                    $categories[] = $currentProductCategory->name ?? '';

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
                    WarehouseLog::create([
                        'from_warehouse_id' => $request->from_warehouse,
                        'to_warehouse_id' => $request->to_warehouse,
                        'quantity' => $currentQuantity,
                        'entry_date' => $currentEntryDate,
                        'product_id' => $currentProduct,
                        'subcategory_id' => $fromWarehouse->subcategory_id,
                        'company_id' => $fromWarehouse->company_id
                    ]);
                } else {
                    DB::rollBack();
                    return redirect()->route('dashboard.index')
                        ->with('error', 'Anbarda kifayət qədər məhsul yoxdur.');
                }
            }
            DB::commit();
            // export pdf
            $path = public_path('images/signature.png');
            $imageData = base64_encode(file_get_contents($path));
            $imageType = pathinfo($path, PATHINFO_EXTENSION);
            $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;
            $data = [
                'to' => "Sabirabad filialının Direktoru Məmmədov Xalid Əlioğlan oğlu",
                'quantities' => $request->quantities,
                'transfer_date' => Carbon::parse($request->transfer_dates[0])->format('d.m.Y'),
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
                'quantities.*' => 'nullable|integer',

                'categories' => 'required|array|min:1',
                'categories.*' => 'nullable|string',

                'entry_dates' => 'required|array|min:1',
                'entry_dates.*' => 'required|date',
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

            // loop through all arrays 
            $loopLength = count($request->products);
            for ($i = 0; $i < $loopLength; $i++) {
                if(!($request->products[$i] && $request->quantities[$i] && $request->categories[$i])){
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
                $currentProduct = $request->products[$i];
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
                        'subcategory_id' => $categoryId,
                        'entry_date' => $request->entry_dates[$i]
                    ]);
                }
                WarehouseLog::create([
                    'to_warehouse_id' => $warehouseId,
                    'quantity' => $request->quantities[$i],
                    'entry_date' => $request->entry_dates[$i],
                    'product_id' => $productId,
                    'subcategory_id' => $categoryId,
                    'company_id' => $companyId
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
        $query = ProductEntry::query();

        $warehouseId = $request->warehouse_id ?: Warehouse::getMainWarehouse()->id;

        if ($request->product_id) {
            $query->where('product_entries.product_id', $request->product_id);
        }

        if ($request->category_id) {
            $query->where('p.subcategory_id', $request->category_id);
        }

        if ($request->get('export_type') === 'all') {
            $products = $query->select('product_id', 'warehouse_id', 'quantity', 's.name as subcategory_name')
                ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
                ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
                ->where('product_entries.warehouse_id', $warehouseId)
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 20;
            $products = $query->select('product_id', 'warehouse_id', 'quantity', 's.name as subcategory_name')
                ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
                ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
                ->where('product_entries.warehouse_id', $warehouseId)
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
        $pdf = Pdf::loadView('pages.pdf.main', $data)->setPaper('a4')
            ->setOptions(['defaultFont' => 'DejaVu Sans']);

        // Return the generated PDF
        return $pdf->download('products.pdf');
    }

    public function exportEntryProducts(Request $request)
    {
        $query = WarehouseLog::query();

        if ($request->warehouse_id) {
            $warehouseId = $request->warehouse_id;
        } else {
            $mainWarehouse = Warehouse::getMainWarehouse();
            $warehouseId = $mainWarehouse->id;
        }
        if ($request->from_warehouse_id) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('warehouse_logs.entry_date', [$request->start_date, $request->end_date]);
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
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        if ($request->category_id) {
            $query->where('subcategory_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('warehouse_logs.entry_date', [$request->start_date, $request->end_date]);
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

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
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
            $query->whereBetween('warehouse_logs.entry_date', [$request->start_date, $request->end_date]);
        }


        if ($request->get('export_type') === 'all') {
            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "p.name as product_name",
                    "p.code as product_code",
                    "h.code as highway_code",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.to_warehouse_id",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->where('warehouse_logs.to_warehouse_id', $warehouseId)
                ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 20;

            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "p.name as product_name",
                    "p.code as product_code",
                    "h.code as highway_code",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.to_warehouse_id",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'subcategory_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->where('warehouse_logs.to_warehouse_id', $warehouseId)
                ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId)
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
