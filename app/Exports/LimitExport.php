<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LimitExport implements FromArray, WithHeadings, WithStyles
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
                'İllik tələbat' => $product->demand,
                'Sifariş olunub' => $product->is_ordered ? "Bəli" : "Xeyr",
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
            'İllik tələbat',
            'Sifariş olunub'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->data) + 1; // +1 for header row
        for ($i = 2; $i <= $rowCount; $i++) {
            $quantity = $this->data[$i - 2]->quantity;
            $limit = $this->data[$i - 2]->limit;
            $isOrdered = $this->data[$i - 2]->is_ordered;

            if ($isOrdered) {
                $sheet->getStyle("A{$i}:G{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FFFF00'],
                    ],
                ]);
            }else if($limit >= $quantity && $limit != 0){
                $sheet->getStyle("A{$i}:G{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FF0000'],
                    ],
                ]);
            }
        }
        return [];
    }
}
