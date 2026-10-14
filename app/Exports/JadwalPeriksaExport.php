<?php

namespace App\Exports;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalPeriksaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $idDokter;
    private int $rowNumber = 0;

    public function __construct($idDokter)
    {
        $this->idDokter = $idDokter;
    }

    /**
     * Ambil jadwal milik dokter yang sedang login
     */
    public function collection(): Enumerable
    {
        return JadwalPeriksa::where('id_dokter', $this->idDokter)
            ->with(['daftarPolis.periksas'])
            ->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'NO',
            'HARI PRAKTIK',
            'JAM MULAI',
            'JAM SELESAI',
            'TOTAL PASIEN TERDAFTAR',
            'NOMOR SEDANG DILAYANI'
        ];
    }

    /**
     * Pemetaan data baris per baris
     */
    public function map(mixed $jadwal): array
    {
        $this->rowNumber++;

        // Hitung nomor sedang dilayani
        $sedangDilayani = DaftarPoli::where('id_jadwal', $jadwal->id)
            ->doesntHave('periksas')
            ->orderBy('no_antrian', 'asc')
            ->value('no_antrian');

        if (!$sedangDilayani) {
            $adaSelesai = DaftarPoli::where('id_jadwal', $jadwal->id)->has('periksas')->exists();
            $sedangDilayani = $adaSelesai ? 'Selesai' : '-';
        }

        return [
            $this->rowNumber,
            $jadwal->hari,
            date('H:i', strtotime($jadwal->jam_mulai)) . ' WIB',
            date('H:i', strtotime($jadwal->jam_selesai)) . ' WIB',
            $jadwal->daftarPolis ? $jadwal->daftarPolis->count() : 0,
            $sedangDilayani
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
