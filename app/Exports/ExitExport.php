<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExitExport implements FromArray, WithHeadings
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
                'Anbara' => $product->to_warehouse,
                'Kateqoriya' => $product->subcategory_name,
                'Şassi nömrəsi' => $product->highway_code,
                'Çıxış tarixi' => $product->exit_date
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
            'Anbara',
            'Kateqoriya',
            'Şassi nömrəsi',
            'Çıxış tarixi'
        ];
    }
}
