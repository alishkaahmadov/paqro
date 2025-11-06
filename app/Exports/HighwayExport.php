<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HighwayExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Map only the required columns
        return $this->data->map(function ($highway) {
            return [
                'Nömrəsi' => $highway->code,
                'Məhsul' => $highway->product_name . ($highway->product_code ? ' - ' . $highway->product_code : ''),
                'Kateqoriya' => $highway->category_name,
                'Sayı' => $highway->quantity,
                'Ölçü vahidi' => $highway->measure,
                'Moto saat' => $highway->moto_saat,
                'Anbardan' => $highway->from_warehouse,
                'Tarix' => $highway->entry_date,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Nömrəsi',
            'Məhsul',
            'Kateqoriya',
            'Sayı',
            'Ölçü vahidi',
            'Moto saat',
            'Anbardan',
            'Tarix',
        ];
    }
}
