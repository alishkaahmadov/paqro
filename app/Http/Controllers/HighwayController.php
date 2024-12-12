<?php

namespace App\Http\Controllers;

use App\Models\Highway;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Warehouse;
use App\Models\WarehouseLog;
use Illuminate\Http\Request;
use Throwable;

class HighwayController extends Controller
{

    public function __construct()
    {
        // Apply middleware only to the create method
        $this->middleware('isAdmin')->only(['create', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // code, product_name, quantity, exit_date, pdf
        $products = Product::all();
        $warehouses = Warehouse::all();

        $query = Highway::query();

        if ($request->product_id) {
            $query->where('pe.product_id', $request->product_id);
        }

        if ($request->from_warehouse_id) {
            $query->where('pe.warehouse_id', $request->from_warehouse_id);
        }

        if ($request->highway_code) {
            $query->where('highways.code', $request->highway_code);
        }

        $highways = $query->select(
            "p.name as product_name",
            "p.code as product_code",
            "w.name as warehouse_name",
            "highways.code",
            "highways.quantity",
            "highways.entry_date",
            "highways.pdf_file"
        )
            ->leftJoin('product_entries as pe', 'pe.id', '=', 'highways.product_entry_id')
            ->leftJoin('products as p', 'p.id', '=', 'pe.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'pe.warehouse_id')
            ->orderBy('highways.id', 'desc')
            ->paginate(10)->appends($request->query());
            return view('pages.highway.index', [
            'highways' => $highways,
            'products' => $products,
            'warehouses' => $warehouses,
            'from_warehouse_id' => $request->from_warehouse_id ?? null,
            'highway_code' => $request->highway_code ?? null,
            'product_id' => $request->product_id ?? null
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        return view('pages.highway.create', [
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'code' => 'required|string',
            'quantity' => 'required|integer',
            'pdf' => 'nullable|mimes:pdf|max:51200', // 50mb,
            'date' => 'required|date'
        ]);
        try {
            // check quantity
            $warehouse = ProductEntry::where(['warehouse_id' => $request->warehouse_id, 'product_id' => $request->product_id])->first();
            if ($warehouse->quantity >= $request->quantity) {
                $warehouse->update(['quantity' => $warehouse->quantity - $request->quantity]);
                if($request->pdf){
                    $fileName = time() . '_' . $request->pdf->getClientOriginalName();
                    $filePath = $request->pdf->storeAs('uploads', $fileName, 'public');
                }
                // create
                $highway = Highway::create([
                    'code' => $request->code,
                    'product_entry_id' => $warehouse->id,
                    'pdf_file' => $filePath ?? null,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->date
                ]);
                WarehouseLog::create([
                    'from_warehouse_id' => $request->warehouse_id,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->date,
                    'product_id' => $request->product_id,
                    'company_id' => $warehouse->company_id,
                    'subcategory_id' => $warehouse->subcategory_id,
                    'highway_id' => $highway->id
                ]);
                return redirect()->route('highways.index')
                    ->with('success', 'Şassi əlavə olundu.');
            } else {
                return redirect()->route('dashboard.index')
                ->with('error', 'Anbarda kifayət qədər məhsul yoxdur.');
            }
        } catch (Throwable $th) {
            dd($th->getMessage());
            return redirect()->route('dashboard.index')
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
}
