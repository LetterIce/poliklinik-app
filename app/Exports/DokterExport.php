<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DokterExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    /**
     * Ambil semua data user dengan role dokter
     */
    public function collection(): Enumerable
    {
        return User::where('role', 'dokter')
            ->with('poli')
            ->orderBy('nama', 'asc')
            ->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'NO',
            'NAMA DOKTER',
            'POLIKLINIK',
            'ALAMAT',
            'NO. KTP',
            'NO. HP',
            'EMAIL'
        ];
    }

    /**
     * Pemetaan data baris per baris
     */
    public function map(mixed $dokter): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $dokter->nama,
            optional($dokter->poli)->nama_poli ?? '-',
            $dokter->alamat ?? '-',
            "'" . ($dokter->no_ktp ?? '-'),
            "'" . ($dokter->no_hp ?? '-'),
            $dokter->email
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
