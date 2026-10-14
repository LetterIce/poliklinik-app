<x-layouts.app title="Riwayat Pembayaran">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pembayaran & Tagihan
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftar tagihan pemeriksaan medis dan status verifikasi pembayaran Anda.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
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
                            <th class="px-6 py-4 w-14 text-center">No</th>
                            <th class="px-6 py-4">Poli & Dokter</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Total Biaya</th>
                            <th class="px-6 py-4 text-center">Status Pembayaran</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($periksas as $index => $periksa)
                        @php
                            $pembayaran = $periksa->pembayaran;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-slate-400 font-medium text-xs">
                                {{ $index + 1 }}
                            </td>

                            {{-- Poli & Dokter --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ optional($periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Dokter: {{ optional($periksa->daftarPoli->jadwalPeriksa->dokter)->nama ?? '-' }}
                                </div>
                            </td>

                            {{-- Tanggal Periksa --}}
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                <div class="font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('H:i') }} WIB
                                </div>
                            </td>

                            {{-- Total Biaya --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 font-mono text-sm">
                                    Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status Pembayaran --}}
                            <td class="px-6 py-4 text-center">
                                @if(!$pembayaran)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-exclamation-circle text-xs"></i>
                                        Belum Bayar
                                    </span>
                                @elseif($pembayaran->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-clock text-xs"></i>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($pembayaran->status === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Lunas
                                    </span>
                                @elseif($pembayaran->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle text-xs"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if(!$pembayaran || $pembayaran->status === 'ditolak')
                                    <a href="{{ route('pasien.pembayaran.create', $periksa->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                        <i class="fas fa-upload text-xs"></i>
                                        Upload Bukti Bayar
                                    </a>
                                @else
                                    <div class="inline-flex items-center gap-2">
                                        @if($pembayaran->bukti_bayar)
                                        <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition">
                                            <i class="fas fa-file-image text-xs"></i>
                                            Lihat Bukti
                                        </a>
                                        @endif
                                        @if($pembayaran->status === 'lunas')
                                        <span class="text-xs font-bold text-emerald-600">Terverifikasi</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-slate-400">
                                <i class="fas fa-file-invoice text-3xl mb-3 block text-slate-300"></i>
                                Belum ada riwayat tagihan pemeriksaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>
