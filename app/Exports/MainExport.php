<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MainExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Map only the required columns
        return $this->data->map(function ($product) {
            return [
                'Kod' => $product->product_code,
                'Məhsul' => $product->product_name,
                'Giriş' => $product->entry_total ?? 0,
                'Çıxış' => $product->exit_total ?? 0,
                'Qalıq' => $product->quantity ?? 0,
                'Kateqoriya' => $product->subcategory_name,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Kod',
            'Məhsul',
            'Giriş',
            'Çıxış',
            'Qalıq',
            'Kateqoriya',
        ];
    }
}
