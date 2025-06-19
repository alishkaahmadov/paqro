<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LimitExport implements FromArray, WithHeadings
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
                'Qalıq' => $product->quantity,
                'Ölçü vahidi' => $product->measure,
                'Kateqoriya' => $product->category_name,
                'Limit' => $product->limit,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Kod',
            'Məhsul',
            'Qalıq',
            'Ölçü vahidi',
            'Kateqoriya',
            'Limit',
        ];
    }
}
