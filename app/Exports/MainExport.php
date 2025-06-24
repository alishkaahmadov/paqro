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
                'Qədərki qalıq' => $product->total_before_start_date ?? 0,
                'Giriş' => $product->entry_total ?? 0,
                'Çıxış' => $product->exit_total ?? 0,
                'Qalıq' => ($product->total_before_start_date + $product->entry_total - $product->exit_total) ?? 0,
                'Ölçü vahidi' => $product->measure,
                'Kateqoriya' => $product->subcategory_name,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Kod',
            'Məhsul',
            'Qədərki qalıq',
            'Giriş',
            'Çıxış',
            'Qalıq',
            'Ölçü vahidi',
            'Kateqoriya',
        ];
    }
}
