<x-layouts.app title="Riwayat Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftar pemeriksaan pasien yang telah selesai ditangani.</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-24 text-center">No Antrian</th>
                            <th class="px-6 py-4">Nama Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Total Biaya</th>
                            <th class="px-6 py-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatPasien as $riwayat)
                        <tr class="hover:bg-slate-50/80 transition">

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-[#2d4499] font-bold text-sm border border-blue-100">
                                    {{ $riwayat->daftarPoli->no_antrian }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ $riwayat->daftarPoli->pasien->nama }}
                                </div>
                                @if(optional($riwayat->daftarPoli->pasien)->no_rm)
                                <div class="text-xs text-slate-400 font-mono mt-0.5">
                                    RM: {{ $riwayat->daftarPoli->pasien->no_rm }}
                                </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-slate-600 max-w-xs">
                                <span class="line-clamp-2">
                                    {{ $riwayat->daftarPoli->keluhan ?: '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-600 text-sm">
                                <div class="font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($riwayat->tgl_periksa)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($riwayat->tgl_periksa)->format('H:i') }} WIB
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-emerald-600 font-mono text-sm bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    Rp {{ number_format($riwayat->biaya_periksa, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('riwayat-pasien.show', $riwayat) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-xl text-xs font-semibold shadow-sm transition hover:shadow cursor-pointer">
                                    <i class="fas fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block text-slate-300"></i>
                                Belum ada riwayat pemeriksaan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>