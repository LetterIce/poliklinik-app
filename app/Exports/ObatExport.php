<?php

namespace App\Exports;

use App\Models\Obat;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObatExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    /**
     * Ambil seluruh data obat
     */
    public function collection(): Enumerable
    {
        return Obat::orderBy('nama_obat', 'asc')->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'NO',
            'NAMA OBAT',
            'KEMASAN',
            'STOK',
            'HARGA SATUAN (RP)',
            'TOTAL NILAI ASET (RP)'
        ];
    }

    /**
     * Pemetaan data baris per baris
     */
    public function map(mixed $obat): array
    {
        $this->rowNumber++;
        $stok = $obat->stok ?? 0;
        $harga = $obat->harga ?? 0;

        return [
            $this->rowNumber,
            $obat->nama_obat,
            $obat->kemasan ?? '-',
            $stok,
            $harga,
            $stok * $harga
        ];
    }

    /**
     * Styling baris header
     */
    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2D4499']
                ]
            ],
        ];
    }
}
