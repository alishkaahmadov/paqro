<?php

namespace App\Http\Controllers;

use App\Models\Highway;
use App\Models\HighwayProduct;
use App\Models\Log;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Warehouse;
use App\Models\WarehouseLog;
use Carbon\Carbon;
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
        // $products = Product::all();
        $warehouses = Warehouse::all();

        $query = Highway::query();

        // if ($request->product_id) {
        //     $query->where('pe.product_id', $request->product_id);
        // }

        if ($request->from_warehouse_id) {
            $query->where('highways.belong_to_warehouse_id', $request->from_warehouse_id);
        }

        if ($request->highway_code) {
            $query->where('highways.code', $request->highway_code);
        }

        $highways = $query->select(
            "highways.id as id",
            "w.name as warehouse_name",
            "highways.code",
            // "p.name as product_name",
            // "p.code as product_code",
            // "highways.quantity",
            // "highways.entry_date",
            // "highways.pdf_file"
        )
            // ->leftJoin('product_entries as pe', 'pe.id', '=', 'highways.product_entry_id')
            // ->leftJoin('products as p', 'p.id', '=', 'pe.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'highways.belong_to_warehouse_id')
            ->orderBy('highways.id', 'desc')
            ->paginate(10)->appends($request->query());

            return view('pages.highway.index', [
            'highways' => $highways,
            'warehouses' => $warehouses,
            'from_warehouse_id' => $request->from_warehouse_id ?? null,
            'highway_code' => $request->highway_code ?? null,
            // 'products' => $products,
            // 'product_id' => $request->product_id ?? null
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
                
                if(!$highway = Highway::where('code', $request->code)->first()){
                    // create
                    $highway = Highway::create([
                        'code' => $request->code,
                        'belong_to_warehouse_id' => $request->warehouse_id
                    ]);
                }

                $highwayProduct = $highway->products()->create([
                    'product_entry_id' => $warehouse->id,
                    'pdf_file' => $filePath ?? null,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->date,
                    'from_warehouse_id' => $request->warehouse_id
                ]);
                WarehouseLog::create([
                    'from_warehouse_id' => $request->warehouse_id,
                    'quantity' => $request->quantity,
                    'entry_date' => $request->date,
                    'product_id' => $request->product_id,
                    'company_id' => $warehouse->company_id,
                    'subcategory_id' => $warehouse->subcategory_id,
                    'highway_id' => $highway->id,
                    'highway_product_id' => $highwayProduct->id
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
    public function show(Highway $highway, Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $products = Product::all();

        $query = HighwayProduct::query();

        if ($request->product_id) {
            $query->where('pe.product_id', $request->product_id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('highway_products.entry_date', [$startDate, $endDate]);
        }

        $highways = $query->select(
            "highway_products.id as id",
            "h.code as code",
            "p.name as product_name",
            "p.code as product_code",
            "w.name as from_warehouse",
            "highway_products.quantity",
            "highway_products.entry_date",
            "highway_products.pdf_file"
        )
            ->leftJoin('product_entries as pe', 'pe.id', '=', 'highway_products.product_entry_id')
            ->leftJoin('products as p', 'p.id', '=', 'pe.product_id')
            ->leftJoin('highways as h', 'h.id', '=', 'highway_products.highway_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'highway_products.from_warehouse_id')
            ->where('h.code', $highway->code)
            ->orderBy('highway_products.id', 'desc')
            ->paginate(10)->appends($request->query());

            return view('pages.highway.show', [
            'highwayId' => $highway->id,
            'highways' => $highways,
            'products' => $products,
            'product_id' => $request->product_id ?? null,
            'start_date' => $startDate ?? null,
            'end_date' => $endDate ?? null
        ]);
    }

    public function changeWarehousePage(Highway $highway){
        $warehouses = Warehouse::all();
        return view('pages.highway.change', [
            'warehouses' => $warehouses,
            'highwayId' => $highway->id,
            'current_warehouse' => $highway->belong_to_warehouse_id
        ]);
    }

    public function changeWarehouse(Highway $highway, Request $request){
        $oldBelongWarehouse = $highway->belongWarehouse?->name;
        $highway->update(['belong_to_warehouse_id' => $request->current_warehouse]);
        $highway->refresh();
        Log::create([
            'user_id' => auth()->id(),
            'action' => 'Yerdəyişmə etdi',
            'model_type' => 'App\Models\Highway',
            'model_id' => $highway->id,
            'changes' => json_encode(["action" => "$highway->code nömrəli şassiyə yerdəyişmə etdi", "data" => [
                "Anbardan" => $oldBelongWarehouse . " ===> " . $highway->belongWarehouse?->name,
                ]], JSON_UNESCAPED_UNICODE)
        ]);
        return redirect()->route('highways.index')->with('success', 'Yerdəyişmə uğurlu oldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HighwayProduct $highway)
    {
        return view('pages.highway.edit', [
            'code' => $highway->highway?->code,
            'highwayProduct' => $highway,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HighwayProduct $highway, Request $request)
    {
        $productEntry = ProductEntry::where('id', $highway->product_entry_id)->first();
        $warehouseLog = WarehouseLog::where('highway_product_id', $highway->id)->first();
        if($productEntry && $warehouseLog){
            $productEntry->update(['quantity' => $productEntry->quantity + ($highway->quantity - $request->quantity)]);
            $warehouseLog->update(['quantity' => $request->quantity]);

            if($highway->highway?->code == $request->code){
                $highway->update([
                    'quantity' => $request->quantity,
                    'entry_date' => $request->entry_date
                ]);
            }else{
                $checkHighwayCode = Highway::where('code', $request->code)->first();
                if($checkHighwayCode){
                    $highway->update([
                        'highway_id' => $checkHighwayCode->id,
                        'quantity' => $request->quantity,
                        'entry_date' => $request->entry_date
                    ]);
                }else{
                    $newHighway = Highway::create([
                        'code' => $request->code,
                        'belong_to_warehouse_id' => $highway->highway?->belong_to_warehouse_id
                    ]);
                    $newHighway->products()->create([
                        'product_entry_id' => $highway->product_entry_id,
                        'pdf_file' => $highway->pdf_file,
                        'quantity' => $request->quantity,
                        'entry_date' => $request->entry_date
                    ]);
                }
            }
        }
        return redirect()->route('highways.index')->with('success', 'Uğurlu oldu.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
