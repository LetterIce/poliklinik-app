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

class PasienExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    /**
     * Ambil semua data user dengan role pasien
     */
    public function collection(): Enumerable
    {
        return User::where('role', 'pasien')
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
            'NAMA PASIEN',
            'NO. REKAM MEDIS (RM)',
            'ALAMAT',
            'NO. KTP',
            'NO. HP',
            'EMAIL'
        ];
    }

    /**
     * Pemetaan data baris per baris
     */
    public function map(mixed $pasien): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $pasien->nama,
            $pasien->no_rm ?? '-',
            $pasien->alamat ?? '-',
            "'" . ($pasien->no_ktp ?? '-'),
            "'" . ($pasien->no_hp ?? '-'),
            $pasien->email
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
