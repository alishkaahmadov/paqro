<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Throwable;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        return view('pages.product.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subcategories = Subcategory::all();
        return view('pages.product.create', ['subcategories' => $subcategories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'code' => 'string|nullable',
            'dnn' => 'string|nullable',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);
        try {
            Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'code' => $request->code ?? null,
                'dnn' => $request->dnn ?? null,
                'subcategory_id' => $request->subcategory_id
            ]);
            return redirect()->route('products.index')
                ->with('success', 'Məhsul uğurla yaradıldı.');
        } catch (Throwable $th) {
            return redirect()->route('products.index')
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
        $product = Product::findOrFail($id);
        return view('pages.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255'
        ]);

        $warehouse = Product::findOrFail($id);
        $warehouse->name = $request->name;
        $warehouse->code = $request->code;
        $warehouse->save();

        return redirect()->route('products.index')->with('success', 'Uğurla düzəliş olundu.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
