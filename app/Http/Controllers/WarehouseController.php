<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Throwable;

class WarehouseController extends Controller
{

    public function __construct()
    {
        // Apply middleware only to the create method
        $this->middleware('isAdmin')->only(['create', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::orderBy('is_main', 'desc')->paginate(10);
        return view('pages.warehouse.index', ['warehouses' => $warehouses]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.warehouse.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'warehouseman' => 'required|string'
        ]);
        try {
            Warehouse::create(['name' => $request->name, 'warehouseman' => $request->warehouseman, 'is_main' => $request->is_main ? true : false]);
            return redirect()->route('warehouses.index')
                ->with('success', 'Anbar uğurla yaradıldı.');
        } catch (Throwable $th) {
            return redirect()->route('warehouses.index')
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
        $warehouse = Warehouse::findOrFail($id);
        return view('pages.warehouse.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'warehouseman' => 'required|string|max:255',
            'is_main' => 'required|boolean',
        ]);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->name = $request->name;
        $warehouse->warehouseman = $request->warehouseman;
        $warehouse->is_main = $request->is_main;
        $warehouse->save();

        return redirect()->route('warehouses.index')->with('success', 'Uğurla düzəliş olundu.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        if($warehouse->is_main) return redirect()->route('warehouses.index')->with('error', 'Əsas anbar silinə bilməz.');
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Uğurla silindi.');
    }
}
