<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Subcategory;
use App\Models\Warehouse;
use App\Models\WarehouseLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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

        if ($request->product_id) {
            $query->where('product_entries.product_id', $request->product_id);
        }

        if ($request->category_id) {
            $query->where('product_entries.subcategory_id', $request->category_id);
        }

        $groupedEntries = $query->select('product_id', 'subcategory_id', 'warehouse_id', 'quantity')
            ->where('product_entries.warehouse_id', $warehouseId)
            ->paginate(10);
        return view('pages.dashboard.index', [
            'productEntries' => $groupedEntries,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_id' => $request->product_id ?? null,
            'categories' => $categories,
            'category_id' => $request->category_id ?? null,
        ]);
    }

    public function entries(Request $request)
    {
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

        // warehouse_logs 
        $warehouseLogs = $query
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
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'warehouse_logs.subcategory_id')
            ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
            ->where('warehouse_logs.to_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->paginate(10);

        return view('pages.dashboard.entries', [
            'productEntries' => $warehouseLogs,
            'products' => $products,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'companies' => $companies,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_id' => $request->product_id ?? null,
            'company_id' => $request->company_id ?? null,
            'category_id' => $request->category_id ?? null,
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null
        ]);
    }

    public function exits(Request $request)
    {
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

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        // if ($request->dnn_code) {
        //     $query->where('d.code', $request->dnn_code);
        // }
        if ($request->category_id) {
            $query->where('p.subcategory_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('warehouse_logs.entry_date', [$request->start_date, $request->end_date]);
        }

        // warehouse_logs 
        $warehouseLogs = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                "p.code as product_code",
                "p.name as product_name",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "h.code as highway_code",
                // "d.code as dnn_code",
                "warehouse_logs.entry_date as exit_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
            // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
            ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
            ->where('warehouse_logs.from_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->paginate(10);
        return view('pages.dashboard.exits', [
            'productExits' => $warehouseLogs,
            'products' => $products,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_id' => $request->product_id ?? null,
            'highway_code' => $request->highway_code ?? null,
            // 'dnn_code' => $request->dnn_code ?? null,
            'category_id' => $request->category_id ?? null,
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null
        ]);
    }

    public function overall(Request $request)
    {
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

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        // if ($request->dnn_code) {
        //     $query->where('d.code', $request->dnn_code);
        // }
        if ($request->category_id) {
            $query->where('p.subcategory_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('warehouse_logs.entry_date', [$request->start_date, $request->end_date]);
        }

        // warehouse_logs 
        $warehouseLogs = $query
            ->select(
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                "p.name as product_name",
                "p.code as product_code",
                "h.code as highway_code",
                "c.name as company_name",
                // "d.code as dnn_code",
                "sc.name as subcategory_name",
                "warehouse_logs.quantity",
                "warehouse_logs.to_warehouse_id",
                "warehouse_logs.entry_date as entry_date"
            )
            ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
            // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
            ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
            ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
            ->where('warehouse_logs.to_warehouse_id', $warehouseId)
            ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId)
            ->orderBy('warehouse_logs.entry_date', 'desc')
            ->paginate(10);

        return view('pages.dashboard.overall', [
            'productEntries' => $warehouseLogs,
            'warehouseName' => $warehouseName,
            'warehouseId' => $warehouseId,
            'products' => $products,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'companies' => $companies,
            'highway_code' => $request->highway_code ?? null,
            // 'dnn_code' => $request->dnn_code ?? null,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_id' => $request->product_id ?? null,
            'company_id' => $request->company_id ?? null,
            'category_id' => $request->category_id ?? null,
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null
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

        return view('pages.dashboard.create', [
            'products' => $products,
            'warehouses' => $warehouses,
            'companies' => $companies
        ]);
    }

    public function getProducts($warehouseId)
    {
        $products = ProductEntry::select(
            "p.id as product_id",
            "p.name as product_name",
            "p.code as product_code",
            "product_entries.quantity"
        )
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->where('product_entries.warehouse_id', $warehouseId)
            ->get();

        return response()->json($products);
    }

    public function transferPage()
    {
        $warehouses = Warehouse::all();

        return view('pages.dashboard.transfer', [
            'warehouses' => $warehouses
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_warehouse' => 'required|exists:warehouses,id',
            'to_warehouse' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'transfer_date' => 'required|date'
        ]);
        try {
            // check quantity
            $fromWarehouse = ProductEntry::where(['warehouse_id' => $request->from_warehouse, 'product_id' => $request->product_id])->first();
            if ($fromWarehouse->quantity >= $request->quantity) {
                $fromWarehouse->update(['quantity' => $fromWarehouse->quantity - $request->quantity]);
                $toWarehouse = ProductEntry::where(['warehouse_id' => $request->to_warehouse, 'product_id' => $request->product_id])->first();
                if ($toWarehouse) {
                    // increase
                    $toWarehouse->update(['quantity' => $toWarehouse->quantity + $request->quantity]);
                } else {
                    // create
                    ProductEntry::create([
                        'warehouse_id' => $request->to_warehouse,
                        'product_id' => $request->product_id,
                        'company_id' => $fromWarehouse->company_id,
                        'quantity' => $request->quantity,
                        'entry_date' => $request->transfer_date
                    ]);
                }
                WarehouseLog::create([
                    'from_warehouse_id' => $request->from_warehouse,
                    'to_warehouse_id' => $request->to_warehouse,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->transfer_date,
                    'product_id' => $request->product_id,
                    'company_id' => $fromWarehouse->company_id
                ]);
                return redirect()->route('dashboard.index')
                    ->with('success', 'Məhsul uğurla əlavə edildi.');
            } else {
                return redirect()->route('dashboard.index')
                    ->with('error', 'Anbarda kifayət qədər məhsul yoxdur.');
            }
        } catch (Throwable $th) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Xəta baş verdi.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'company_id' => 'required|exists:companies,id',
            'quantity' => 'required|integer',
            'entry_date' => 'required|date'
        ]);
        try {
            //check if warehouse has current product => if has increase quantity if not create one

            $productEntry = ProductEntry::where(['warehouse_id' => $request->warehouse_id, 'product_id' => $request->product_id])->first();
            if ($productEntry) {
                // increase
                $productEntry->update(['quantity' => $productEntry->quantity + $request->quantity]);
            } else {
                // create
                ProductEntry::create([
                    'warehouse_id' => $request->warehouse_id,
                    'product_id' => $request->product_id,
                    'company_id' => $request->company_id,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->entry_date
                ]);
            }
            WarehouseLog::create([
                'to_warehouse_id' => $request->warehouse_id,
                'quantity' => $request->quantity,
                'entry_date' => $request->entry_date,
                'product_id' => $request->product_id,
                'company_id' => $request->company_id
            ]);
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
            $products = $query->select('product_id', 'warehouse_id', 'quantity')
                ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
                ->where('product_entries.warehouse_id', $warehouseId)
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 10;
            $products = $query->select('product_id', 'warehouse_id', 'quantity')
                ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
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

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->category_id) {
            $query->where('p.subcategory_id', $request->category_id);
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
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
                ->leftJoin('companies as c', 'c.id', '=', 'warehouse_logs.company_id')
                ->where('warehouse_logs.to_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 10;
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
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
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

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->highway_code) {
            $query->where('h.code', $request->highway_code);
        }
        // if ($request->dnn_code) {
        //     $query->where('d.code', $request->dnn_code);
        // }
        if ($request->category_id) {
            $query->where('p.subcategory_id', $request->category_id);
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
                    // "d.code as dnn_code",
                    "warehouse_logs.entry_date as exit_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
                // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->where('warehouse_logs.from_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 10;

            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "p.code as product_code",
                    "p.name as product_name",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "h.code as highway_code",
                    // "d.code as dnn_code",
                    "warehouse_logs.entry_date as exit_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
                // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
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
        // if ($request->dnn_code) {
        //     $query->where('d.code', $request->dnn_code);
        // }
        if ($request->category_id) {
            $query->where('p.subcategory_id', $request->category_id);
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
                    // "d.code as dnn_code",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.to_warehouse_id",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
                // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
                ->leftJoin('highways as h', 'h.id', '=', 'warehouse_logs.highway_id')
                ->where('warehouse_logs.to_warehouse_id', $warehouseId)
                ->orWhere('warehouse_logs.from_warehouse_id', $warehouseId)
                ->orderBy('warehouse_logs.entry_date', 'desc')
                ->get();
        } else {
            $page = $request->input('page', 1);
            $perPage = 10;

            $products = $query
                ->select(
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.from_warehouse_id) as from_warehouse"),
                    DB::raw("(select name from warehouses where warehouses.id = warehouse_logs.to_warehouse_id) as to_warehouse"),
                    "p.name as product_name",
                    "p.code as product_code",
                    "h.code as highway_code",
                    // "d.code as dnn_code",
                    "sc.name as subcategory_name",
                    "warehouse_logs.quantity",
                    "warehouse_logs.to_warehouse_id",
                    "warehouse_logs.entry_date as entry_date"
                )
                ->leftJoin('products as p', 'p.id', '=', 'warehouse_logs.product_id')
                ->leftJoin('subcategories as sc', 'sc.id', '=', 'p.subcategory_id')
                // ->leftJoin('dnns as d', 'd.id', '=', 'warehouse_logs.dnn_id')
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
