<x-layouts.app title="Detail Riwayat Pasien">

    {{-- Breadcrumb & Back --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('riwayat-pasien.index') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 shadow-sm transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold text-slate-800">
                        Detail Riwayat Pemeriksaan
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Selesai
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Rincian lengkap hasil pemeriksaan pasien dan resep obat.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-sm font-semibold shadow-sm transition">
                <i class="fas fa-print text-xs"></i>
                <span>Cetak</span>
            </button>
            <a href="{{ route('riwayat-pasien.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left 2 Columns: Data Pasien, Catatan, Obat --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Card Informasi Pasien & Pemeriksaan --}}
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#2d4499] flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Informasi Pasien</h3>
                            <p class="text-[11px] text-slate-500">Identitas pasien dan jadwal antrian</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-[#2d4499] font-bold text-xs border border-blue-200">
                        <i class="fas fa-ticket-alt"></i> No. Antrian: {{ $periksa->daftarPoli->no_antrian }}
                    </span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                        
                        {{-- Nama Pasien --}}
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Nama Pasien</span>
                            <span class="text-base font-bold text-slate-800 block">
                                {{ $periksa->daftarPoli->pasien->nama }}
                            </span>
                            @if(optional($periksa->daftarPoli->pasien)->no_rm)
                            <span class="text-xs text-slate-500 font-mono mt-0.5 block">
                                No. RM: {{ $periksa->daftarPoli->pasien->no_rm }}
                            </span>
                            @endif
                        </div>

                        {{-- Tanggal & Waktu --}}
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Waktu Pemeriksaan</span>
                            <span class="text-base font-bold text-slate-800 block font-mono">
                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d F Y') }}
                            </span>
                            <span class="text-xs text-slate-500 font-mono mt-0.5 block">
                                Pukul {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('H:i') }} WIB
                            </span>
                        </div>

                        {{-- Dokter Pemeriksa --}}
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Dokter Pemeriksa</span>
                            <span class="text-sm font-bold text-slate-800 block">
                                {{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? 'Dokter' }}
                            </span>
                            <span class="text-xs text-slate-500 mt-0.5 block">
                                Poli: {{ optional($periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}
                            </span>
                        </div>

                        {{-- Keluhan Pasien --}}
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Keluhan Pasien</span>
                            <span class="text-sm text-slate-700 italic block">
                                "{{ $periksa->daftarPoli->keluhan ?: 'Tidak ada keluhan tercatat' }}"
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 2. Card Catatan Dokter / Diagnosa --}}
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-[#2d4499] flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Catatan Dokter & Diagnosa</h3>
                        <p class="text-[11px] text-slate-500">Hasil diagnosa dan instruksi medis</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="p-4 rounded-xl bg-slate-50 border-l-4 border-[#2d4499] text-slate-800 text-sm leading-relaxed">
                        @if(!empty($periksa->catatan))
                            {{ $periksa->catatan }}
                        @else
                            <span class="text-slate-400 italic">Tidak ada catatan diagnosa tambahan.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. Card Obat yang Diresepkan --}}
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Obat yang Diresepkan</h3>
                            <p class="text-[11px] text-slate-500">Daftar obat yang diberikan untuk pasien</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $periksa->detailPeriksas ? $periksa->detailPeriksas->count() : 0 }} Item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    @if($periksa->detailPeriksas && $periksa->detailPeriksas->count() > 0)
                    <table class="table w-full">
                        <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5 w-16 text-center">#</th>
                                <th class="px-6 py-3.5">Nama Obat</th>
                                <th class="px-6 py-3.5">Kemasan</th>
                                <th class="px-6 py-3.5 text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($periksa->detailPeriksas as $index => $detail)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 text-center text-slate-400 text-xs">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                    {{ $detail->obat->nama_obat ?? 'Obat' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 text-xs">
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-medium">
                                        {{ $detail->obat->kemasan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-600 font-mono text-sm">
                                    Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-10 text-center text-slate-400 text-sm">
                        <i class="fas fa-capsules text-3xl mb-2 text-slate-300 block"></i>
                        Tidak ada resep obat untuk pemeriksaan ini.
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right 1 Column: Billing / Biaya Periksa Summary --}}
        <div class="space-y-6">

            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Rincian Biaya</h3>
                        <p class="text-[11px] text-slate-500">Perhitungan total biaya periksa</p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    
                    {{-- Jasa Dokter --}}
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Biaya Jasa Dokter</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($periksa->biaya_dokter, 0, ',', '.') }}</span>
                    </div>

                    {{-- Subtotal Obat --}}
                    @php
                        $totalHargaObat = 0;
                        if ($periksa->detailPeriksas) {
                            foreach ($periksa->detailPeriksas as $detail) {
                                $totalHargaObat += ($detail->obat->harga ?? 0);
                            }
                        }
                    @endphp
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Biaya Resep Obat ({{ $periksa->detailPeriksas ? $periksa->detailPeriksas->count() : 0 }})</span>
                        <span class="font-semibold text-slate-700 font-mono">
                            Rp {{ number_format($totalHargaObat, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-dashed border-slate-200 pt-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Pembayaran</span>
                        <div class="text-2xl font-black text-[#2d4499] font-mono">
                            Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            *Sudah termasuk tarif jasa dokter dan obat-obatan.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                        <a href="{{ route('riwayat-pasien.index') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-xl text-sm font-semibold shadow-sm transition">
                            <i class="fas fa-arrow-left text-xs"></i>
                            <span>Kembali ke Riwayat</span>
                        </a>
                    </div>

                </div>
            </div>

        </div>

    </div>

</x-layouts.app>