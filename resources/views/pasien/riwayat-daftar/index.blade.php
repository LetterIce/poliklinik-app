<x-layouts.app title="Riwayat Pendaftaran Poli">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pendaftaran Poli
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftar semua pendaftaran antrean dan riwayat pemeriksaan Anda di poliklinik.</p>
        </div>

        <a href="{{ route('pasien.daftar') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold rounded-xl text-sm shadow-sm transition">
            <i class="fas fa-plus text-xs"></i>
            <span>Daftar Periksa Baru</span>
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
    <div class="alert alert-error mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Table Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">No</th>
                            <th class="px-6 py-4">Poliklinik</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Jadwal Periksa</th>
                            <th class="px-6 py-4 text-center">No. Antrian</th>
                            <th class="px-6 py-4 text-center">Status Pemeriksaan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatPendaftaran as $index => $item)
                        @php
                            $sudahDiperiksa = $item->periksas && $item->periksas->isNotEmpty();
                            $periksa = $sudahDiperiksa ? $item->periksas->first() : null;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-slate-400 font-medium text-xs">
                                {{ $riwayatPendaftaran->firstItem() + $index }}
                            </td>

                            {{-- Nama Poli --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 text-[#2d4499] font-bold text-xs border border-blue-100">
                                    <i class="fas fa-hospital text-[10px]"></i>
                                    {{ optional($item->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}
                                </span>
                            </td>

                            {{-- Nama Dokter --}}
                            <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                {{ optional($item->jadwalPeriksa->dokter)->nama ?? '-' }}
                            </td>

                            {{-- Tanggal / Jadwal --}}
                            <td class="px-6 py-4 text-slate-700 text-xs font-medium">
                                <div class="font-semibold text-slate-800 text-sm">
                                    {{ $item->jadwalPeriksa->hari ?? '-' }}
                                </div>
                                @if($item->jadwalPeriksa)
                                <div class="text-slate-400 font-mono mt-0.5">
                                    {{ date('H:i', strtotime($item->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($item->jadwalPeriksa->jam_selesai)) }} WIB
                                </div>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Daftar: {{ $item->created_at->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- Nomor Antrian --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-[#2d4499] font-black text-sm border border-blue-200 shadow-xs">
                                    {{ $item->no_antrian }}
                                </span>
                            </td>

                            {{-- Status Pemeriksaan --}}
                            <td class="px-6 py-4 text-center">
                                @if($sudahDiperiksa)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Sudah Diperiksa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-clock text-xs"></i>
                                        Menunggu Pemeriksaan
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($sudahDiperiksa)
                                    <a href="{{ route('pasien.riwayat-daftar.show', $item->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-xl text-xs font-semibold shadow-sm transition hover:shadow">
                                        <i class="fas fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </a>
                                @else
                                    <button disabled
                                        title="Menunggu pemeriksaan dokter selesai"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-xs font-semibold cursor-not-allowed">
                                        <i class="fas fa-hourglass-half text-xs"></i>
                                        <span>Belum Ada Hasil</span>
                                    </button>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <i class="fas fa-calendar-xmark text-3xl mb-3 block text-slate-300"></i>
                                Anda belum memiliki riwayat pendaftaran poliklinik.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if($riwayatPendaftaran->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $riwayatPendaftaran->links() }}
            </div>
            @endif

        </div>
    </div>

</x-layouts.app>
