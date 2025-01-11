<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\Subcategory;
use App\Models\WarehouseLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToModel, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if(!($row[0] && $row[2] && $row[3] && $row[5])) return;
        $currentCompany = $row[0];
        if ($companyExits = Company::where(['name' => $currentCompany])->first()) {
            $companyId = $companyExits->id;
        } else {
            $newCompany = Company::create(['name' => $currentCompany]);
            $companyId = $newCompany->id;
        }

        $currentCategory = $row[5];
        if ($categoryExits = Subcategory::where(['name' => $currentCategory])->first()) {
            $categoryId = $categoryExits->id;
        } else {
            $newCategory = Subcategory::create(['name' => $currentCategory]);
            $categoryId = $newCategory->id;
        }
        if ($productExits = Product::where(['name' => $row[2], 'code' => $row[1]])->first()) {
            $productId = $productExits->id;
            // if product exits check if warehouse has current product => if has increase quantity if not create one
        } else {
            $newProduct = Product::create(['name' => $row[2], 'code' => $row[1]]);
            $productId = $newProduct->id;
        }
        //check if warehouse has current product => if has increase quantity if not create one
        $productEntry = ProductEntry::where(['warehouse_id' => 1, 'product_id' => $productId, 'subcategory_id' => $categoryId])->first();
        if ($productEntry) {
            // increase
            $productEntry->update(['quantity' => $productEntry->quantity + $row[3]]);
        } else {
            // create
            ProductEntry::create([
                'warehouse_id' => 1,
                'product_id' => $productId,
                'company_id' => $companyId,
                'quantity' => $row[3],
                'measure' => $row[4],
                'subcategory_id' => $categoryId,
                'entry_date' => "01.01.2025"
            ]);
        }
        return WarehouseLog::create([
            'to_warehouse_id' => 1,
            'quantity' => $row[3],
            'measure' => $row[4],
            'entry_date' => "01.01.2025",
            'product_id' => $productId,
            'subcategory_id' => $categoryId,
            'company_id' => $companyId
        ]);
    }
    public function chunkSize(): int
    {
        return 100; // Process 1000 rows at a time
    }
}
