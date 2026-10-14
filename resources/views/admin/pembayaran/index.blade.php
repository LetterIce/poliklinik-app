<x-layouts.app title="Verifikasi Pembayaran Pasien">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Verifikasi Pembayaran Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-1">Kelola dan verifikasi bukti transfer pembayaran tagihan pemeriksaan medis.</p>
        </div>

        {{-- Filter Status --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pembayaran.index') }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ !$status ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'menunggu_verifikasi']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'menunggu_verifikasi' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Menunggu Verifikasi
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'lunas']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'lunas' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Lunas
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'ditolak']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'ditolak' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Ditolak
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('message'))
    <div class="alert alert-info mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-info-circle"></i>
        <span>{{ session('message') }}</span>
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
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Pemeriksaan / Poli</th>
                            <th class="px-6 py-4">Jumlah Bayar</th>
                            <th class="px-6 py-4">Bukti Transfer</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-slate-400 font-medium text-xs">
                                {{ $pembayarans->firstItem() + $index }}
                            </td>

                            {{-- Pasien --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ $item->pasien->nama ?? 'Pasien' }}
                                </div>
                                @if(optional($item->pasien)->no_rm)
                                <div class="text-xs text-slate-400 font-mono mt-0.5">
                                    RM: {{ $item->pasien->no_rm }}
                                </div>
                                @endif
                            </td>

                            {{-- Pemeriksaan / Poli --}}
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700 text-sm">
                                    {{ optional($item->periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Dokter: {{ optional($item->periksa->daftarPoli->jadwalPeriksa->dokter)->nama ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->tgl_bayar)->translatedFormat('d M Y, H:i') }}
                                </div>
                            </td>

                            {{-- Jumlah Bayar --}}
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-slate-800 text-sm">
                                    Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->metode_pembayaran }}
                                </div>
                            </td>

                            {{-- Bukti Transfer --}}
                            <td class="px-6 py-4">
                                @if($item->bukti_bayar)
                                <a href="{{ asset('storage/' . $item->bukti_bayar) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-[#2d4499] hover:bg-blue-100 rounded-lg text-xs font-semibold border border-blue-200 transition">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Lihat File</span>
                                </a>
                                @else
                                <span class="text-xs text-slate-400 italic">Tidak ada file</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($item->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-clock text-xs"></i>
                                        Menunggu
                                    </span>
                                @elseif($item->status === 'lunas')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Lunas
                                    </span>
                                @elseif($item->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle text-xs"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($item->status === 'menunggu_verifikasi')
                                    <form action="{{ route('admin.pembayaran.verifikasi', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini sebagai LUNAS? Notifikasi akan otomatis terkirim ke pasien.')"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                            <i class="fas fa-check text-xs"></i>
                                            <span>Verifikasi Lunas</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Selesai diproses</span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <i class="fas fa-receipt text-3xl mb-3 block text-slate-300"></i>
                                Tidak ada data pembayaran ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $pembayarans->links() }}
            </div>
            @endif

        </div>
    </div>

</x-layouts.app>
