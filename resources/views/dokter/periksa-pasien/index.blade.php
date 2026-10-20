<x-layouts.app title="Daftar Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Daftar Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-1">Antrean pasien yang mendaftar pada jadwal praktik Anda.</p>
        </div>
    </div>

    {{-- Alert Success / Message --}}
    @if(session('success') || session('message'))
    <div class="alert alert-success mb-4 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') ?? session('message') }}</span>
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="alert alert-error mb-4 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">No Antrian</th>
                            <th class="px-6 py-4">Nama Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">Jadwal Praktik</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody>
                        @forelse($daftarPasien as $daftar)
                        @php
                            $sudahDiperiksa = $daftar->periksas && $daftar->periksas->count() > 0;
                        @endphp
                        <tr class="hover:bg-slate-50 transition border-b border-slate-100">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-bold text-sm">
                                    {{ $daftar->no_antrian }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $daftar->pasien->nama ?? 'Pasien' }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    No. RM: {{ $daftar->pasien->no_rm ?? '-' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                                {{ $daftar->keluhan ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-slate-600 text-sm">
                                <span class="font-medium text-slate-700">
                                    {{ $daftar->jadwalPeriksa->hari ?? '-' }}
                                </span>
                                @if($daftar->jadwalPeriksa)
                                <span class="text-xs text-slate-400 block font-mono">
                                    {{ date('H:i', strtotime($daftar->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($daftar->jadwalPeriksa->jam_selesai)) }}
                                </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($sudahDiperiksa)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        Sudah Diperiksa
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        Menunggu
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($sudahDiperiksa)
                                    <button disabled class="inline-flex items-center gap-1 px-3.5 py-2 bg-slate-100 text-slate-400 rounded-lg text-xs font-semibold cursor-not-allowed">
                                        <i class="fas fa-check text-xs"></i>
                                        Selesai
                                    </button>
                                @else
                                    <div class="inline-flex items-center gap-2">
                                        <form action="{{ route('periksa-pasien.panggil', $daftar->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" title="Panggil Pasien secara Real-Time via Reverb"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer">
                                                <i class="fas fa-bullhorn text-xs"></i>
                                                Panggil
                                            </button>
                                        </form>
                                        <a href="{{ route('periksa-pasien.create', $daftar->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            <i class="fas fa-stethoscope text-xs"></i>
                                            Periksa
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-slate-400">
                                <i class="fas fa-user-clock text-3xl mb-3 block text-slate-300"></i>
                                Belum ada antrean pasien untuk jadwal praktik Anda.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>
