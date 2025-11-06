<?php

namespace App\Http\Controllers;

use App\Models\ProductEntry;
use App\Models\Subcategory;
use App\Models\Warehouse;
use App\Models\WarehouseLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Subcategory::orderBy('id', 'desc')->paginate(10);
        return view('pages.category.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        try {
            Subcategory::create(['name' => $request->name]);
            return redirect()->route('categories.index')
                ->with('success', 'Subanbar uğurla yaradıldı.');
        } catch (Throwable $th) {
            return redirect()->route('categories.index')
                ->with('error', 'Xəta baş verdi.');
        }
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

    public function listProductCategories(Request $request){
        $warehouses = Warehouse::orderBy('id', 'asc')->get();
        $categories = Subcategory::all();

        if($request->warehouse_id && $request->subcategory_id && $request->product_id){
            $data = DB::table('product_entries')->select([
                'w.name as warehouse_name',
                'p.name as product_name',
                'p.code as product_code',
                'sc.name as subcategory_name',
                'product_entries.quantity',
            ])
            ->leftJoin('products as p', 'p.id', '=', 'product_entries.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'product_entries.warehouse_id')
            ->leftJoin('subcategories as sc', 'sc.id', '=', 'product_entries.subcategory_id')
            ->where([
                ['warehouse_id', $request->warehouse_id],
                ['subcategory_id', $request->subcategory_id],
                ['product_id', $request->product_id]
            ])->get();
        }else{
            $data = [];
        }
        return view('pages.dashboard.changeCategory', [
            'data' => $data,
            'product_id' => $request->product_id ?? null,
            'subcategory_id' => $request->subcategory_id ?? null,
            'warehouse_id' => $request->warehouse_id ?? null,
            'warehouses' => $warehouses,
            'categories' => $categories,
        ]);
    }

    public function updateCategory(Request $request){
        if(!($request->warehouse_id && $request->subcategory_id && $request->product_id && $request->new_subcategory_id)){
            return redirect()->route('dashboard.index')->with('error', 'Lazımi xanalar doldurulmayıb.');
        }
        // Girish ve chixishlar
        WarehouseLog::where(function ($q) use ($request) {
            $q->where('from_warehouse_id', $request->warehouse_id)
              ->orWhere('to_warehouse_id', $request->warehouse_id);
        })
        ->where('subcategory_id', $request->subcategory_id)
        ->where('product_id', $request->product_id)
        ->update(['subcategory_id' => $request->new_subcategory_id]);

        // old Entry
        $oldEntry = ProductEntry::where([
            ['warehouse_id', $request->warehouse_id],
            ['subcategory_id', $request->subcategory_id],
            ['product_id', $request->product_id]
        ])->first();

        // new entry exits?
        $entryExits = ProductEntry::where([
            ['warehouse_id', $request->warehouse_id],
            ['subcategory_id', $request->new_subcategory_id],
            ['product_id', $request->product_id]
        ])->first();

        if($entryExits){
            $entryExits->increment('quantity', $oldEntry->quantity);
            $oldEntry->update(['quantity' => 0]);
        }else{
            ProductEntry::where([
                ['warehouse_id', $request->warehouse_id],
                ['subcategory_id', $request->subcategory_id],
                ['product_id', $request->product_id]
            ])->update(['subcategory_id' => $request->new_subcategory_id]);
        }
        return redirect()->route('dashboard.index')->with('success', 'Məhsulun kateqoriyasi uğurla dəyişildi.');
    }
}
