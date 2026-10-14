<?php

namespace App\Exports;

use App\Models\Periksa;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatPasienExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $idDokter;
    private int $rowNumber = 0;

    public function __construct($idDokter)
    {
        $this->idDokter = $idDokter;
    }

    /**
     * Ambil riwayat periksa pasien yang pernah diperiksa dokter yang sedang login
     */
    public function collection(): Enumerable
    {
        return Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($q) {
            $q->where('id_dokter', $this->idDokter);
        })->with([
            'daftarPoli.pasien',
            'detailPeriksas.obat'
        ])->orderBy('tgl_periksa', 'desc')->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'NO',
            'NO. ANTRIAN',
            'NAMA PASIEN',
            'NO. REKAM MEDIS (RM)',
            'KELUHAN',
            'TANGGAL PERIKSA',
            'CATATAN DOKTER / DIAGNOSA',
            'RESEP OBAT',
            'TOTAL BIAYA (RP)'
        ];
    }

    /**
     * Pemetaan data baris per baris
     */
    public function map(mixed $periksa): array
    {
        $this->rowNumber++;

        // Kumpulkan resep obat menjadi string rapi: "Obat A (2x), Obat B (1x)"
        $obatList = [];
        if ($periksa->detailPeriksas) {
            foreach ($periksa->detailPeriksas as $detail) {
                if ($detail->obat) {
                    $obatList[] = $detail->obat->nama_obat . ($detail->obat->kemasan ? ' (' . $detail->obat->kemasan . ')' : '');
                }
            }
        }
        $resepString = count($obatList) > 0 ? implode(', ', $obatList) : 'Tidak ada obat';

        return [
            $this->rowNumber,
            $periksa->daftarPoli->no_antrian ?? '-',
            optional($periksa->daftarPoli->pasien)->nama ?? '-',
            optional($periksa->daftarPoli->pasien)->no_rm ?? '-',
            $periksa->daftarPoli->keluhan ?: '-',
            \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') . ' WIB',
            $periksa->catatan ?: '-',
            $resepString,
            $periksa->biaya_periksa
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
