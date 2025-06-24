<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntryExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->map(function ($product) {
            return [
                'Kod' => $product->product_code,
                'Məhsul' => $product->product_name,
                'Sayı' => $product->quantity,
                'Ölçü vahidi' => $product->measure,
                'Daxilolma' => $product->from_warehouse ? $product->from_warehouse : ($product->company_name ?? "-"),
                'Kateqoriya' => $product->subcategory_name,
                'Giriş tarixi' => $product->entry_date
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Kod',
            'Məhsul',
            'Sayı',
            'Ölçü vahidi',
            'Daxilolma',
            'Kateqoriya',
            'Giriş tarixi'
        ];
    }
}
