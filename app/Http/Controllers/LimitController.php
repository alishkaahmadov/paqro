<?php

namespace App\Http\Controllers;

use App\Exports\LimitExport;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Subcategory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LimitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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

        switch ($request->color){
            case 'red':
                $query->whereColumn('product_entries.quantity', '<', 'product_entries.limit')->where('product_entries.is_ordered', false);
                break;
            case 'yellow':
                $query->where('product_entries.is_ordered', true);
                break;
            case 'white':
                $query->whereColumn('product_entries.quantity', '>', 'product_entries.limit')->where('product_entries.is_ordered', false);
                break;
            default:
                break;
        }

        $groupedEntries = $query->select([
                'product_entries.id',
                'product_entries.product_id',
                'product_entries.measure',
                'product_entries.limit',
                'product_entries.is_ordered',
                'product_entries.subcategory_id',
                'product_entries.warehouse_id',
                'w.name as warehouse_name',
                'product_entries.quantity',
            ])
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'product_entries.warehouse_id')
            ->when($warehouseId !== "all", function($query) use ($warehouseId){
                $query->where('product_entries.warehouse_id', $warehouseId);
            })
            ->orderByRaw('product_entries.quantity < product_entries.limit AND product_entries.is_ordered = false DESC')
            ->orderBy('is_ordered', 'desc')
            ->orderBy('p.name')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.limit.index', [
            'productEntries' => $groupedEntries,
            'products' => $products,
            'warehouses' => $warehouses,
            'warehouse_id' => $request->warehouse_id ?? null,
            'product_ids' => $request->product_ids ?? null,
            'categories' => $categories,
            'except_category_ids' => $request->except_category_ids ?? null,
            'color' => $request->color ?? null,
            'category_id' => $request->category_id ?? null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(ProductEntry $entry)
    {
        return view('pages.limit.edit', [
            'id' => $entry->id,
            'limit' => $entry->limit,
            'is_ordered' => $entry->is_ordered ? true : false
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductEntry $entry)
    {
        $params = [];
        if ($request->filled('category_id')) {
            $params['category_id'] = $request->input('category_id');
        }
        if ($request->filled('warehouse_id')) {
            $params['warehouse_id'] = $request->input('warehouse_id');
        }
        $entry->update([
            'limit' => $request->limit,
            'is_ordered' => $request->is_ordered ? true : false
        ]);
        return redirect()->route('limit.index', $params)
                     ->with('success', 'Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    public function exportLimitProductsExcel(Request $request){
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

        switch ($request->color){
            case 'red':
                $query->whereColumn('product_entries.quantity', '<', 'product_entries.limit')->where('product_entries.is_ordered', false);
                break;
            case 'yellow':
                $query->where('product_entries.is_ordered', true);
                break;
            case 'white':
                $query->whereColumn('product_entries.quantity', '>', 'product_entries.limit')->where('product_entries.is_ordered', false);
                break;
            default:
                break;
        }


        $products = $query->select([
                'product_entries.measure',
                'product_entries.limit',
                'w.name as warehouse_name',
                'product_entries.quantity',
                'p.name as product_name', 
                'p.code as product_code',
                's.name as category_name'
            ])
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'product_entries.warehouse_id')
            ->leftJoin('subcategories as s', 's.id', '=', 'product_entries.subcategory_id')
            ->when($warehouseId !== "all", function($query) use ($warehouseId){
                $query->where('product_entries.warehouse_id', $warehouseId);
            })
            ->orderByRaw('product_entries.quantity < product_entries.limit AND product_entries.is_ordered = false DESC')
            ->orderBy('is_ordered', 'desc')
            ->orderBy('p.name')
            ->get();

        return Excel::download(new LimitExport($products), 'anbar_limit.xlsx');
    }
}
