<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OverallExport implements FromArray, WithHeadings
{
    protected $data;
    protected $warehouseId;

    public function __construct($data, $warehouseId)
    {
        $this->data = $data;
        $this->warehouseId = $warehouseId;
    }

    public function array(): array
    {
        return $this->data->map(function ($product) {
            return [
                'Kod' => $product->product_code,
                'Məhsul' => $product->product_name,
                'Qədərki qalıq' => $product->residual ?? 0,
                'Sayı' => $product->quantity ?? 0,
                'Qalıq' => $product->residual + ($this->warehouseId == $product->to_warehouse_id ? $product->quantity : -$product->quantity),
                'Şirkət' => $product->company_name,
                'Anbardan' => $product->from_warehouse,
                'Anbara' => $product->to_warehouse,
                'Kateqoriya' => $product->subcategory_name,
                'Şassi nömrəsi' => $product->highway_code,
                'Tip' => $this->warehouseId == $product->to_warehouse_id ? 'Giriş' : 'Çıxış',
                'Tarix' => $product->entry_date
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Kod',
            'Məhsul',
            'Qədərki qalıq',
            'Sayı',
            'Qalıq',
            'Şirkət',
            'Anbardan',
            'Anbara',
            'Kateqoriya',
            'Şassi nömrəsi',
            'Tip',
            'Tarix'
        ];
    }
}
