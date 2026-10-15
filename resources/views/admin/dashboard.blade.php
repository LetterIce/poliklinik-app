<x-layouts.app title="Dashboard Admin">

    {{-- ================= HEADER DASHBOARD ================= --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="text-2xl font-bold text-slate-800">
                    Dashboard Administrator
                </h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Selamat datang kembali, <span class="font-semibold text-slate-700">{{ $admin->nama }}</span>! Pantau performa pelayanan klinik, data medis, dan verifikasi keuangan.
            </p>
        </div>

        {{-- Quick Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.pembayaran.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-sm border border-slate-200 shadow-sm transition">
                <i class="fas fa-file-invoice-dollar text-[#2d4499]"></i>
                <span>Verifikasi Kasir</span>
                @if($pembayaranPendingCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-500 text-white">
                    {{ $pembayaranPendingCount }}
                </span>
                @endif
            </a>

            <div class="dropdown dropdown-end">
                <button tabindex="0" role="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold rounded-xl text-sm shadow-sm transition cursor-pointer">
                    <i class="fas fa-circle-plus"></i>
                    <span>Tambah Data</span>
                    <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                <ul tabindex="0" class="dropdown-content z-30 menu p-2 shadow-xl bg-base-100 rounded-2xl w-52 border border-slate-100 mt-2 text-xs font-semibold">
                    <li><a href="{{ route('dokter.create') }}" class="py-2.5"><i class="fas fa-user-doctor text-blue-600"></i> Tambah Dokter Baru</a></li>
                    <li><a href="{{ route('pasien.create') }}" class="py-2.5"><i class="fas fa-user-injured text-amber-600"></i> Tambah Pasien Baru</a></li>
                    <li><a href="{{ route('obat.create') }}" class="py-2.5"><i class="fas fa-pills text-purple-600"></i> Tambah Data Obat</a></li>
                    <li><a href="{{ route('polis.create') }}" class="py-2.5"><i class="fas fa-hospital text-emerald-600"></i> Tambah Poliklinik</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- ================= BANNER HIGHLIGHTS KEUANGAN & OPERASIONAL ================= --}}
    <div class="relative overflow-hidden rounded-2xl shadow-lg mb-8 p-6 sm:p-8 text-white transition-all"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #2d4499 100%);">
        
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute right-48 -top-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            
            {{-- Col 1: Total Pendapatan Terverifikasi --}}
            <div class="space-y-1">
                <span class="text-xs uppercase tracking-wider text-slate-300 font-bold block">
                    Pendapatan Terverifikasi (Lunas)
                </span>
                <div class="text-3xl sm:text-4xl font-black font-mono text-emerald-400 tracking-tight">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-300 pt-1">
                    Dari total <strong class="text-white">{{ $pembayaranLunasCount }}</strong> transaksi pemeriksaan yang lunas.
                </p>
            </div>

            {{-- Col 2: Pemeriksaan Bulan Ini --}}
            <div class="space-y-1 border-t md:border-t-0 md:border-l border-white/10 pt-4 md:pt-0 md:pl-6">
                <span class="text-xs uppercase tracking-wider text-slate-300 font-bold block">
                    Pemeriksaan Medis Bulan Ini
                </span>
                <div class="text-3xl sm:text-4xl font-black text-white">
                    {{ $pemeriksaanBulanIni }} <span class="text-sm font-normal text-slate-300">Tindakan</span>
                </div>
                <p class="text-xs text-slate-300 pt-1">
                    Periode <strong class="text-white">{{ now()->translatedFormat('F Y') }}</strong>
                </p>
            </div>

            {{-- Col 3: Status Verifikasi Kasir --}}
            <div class="space-y-2 border-t md:border-t-0 md:border-l border-white/10 pt-4 md:pt-0 md:pl-6">
                <span class="text-xs uppercase tracking-wider text-slate-300 font-bold block">
                    Status Verifikasi Kasir
                </span>
                @if($pembayaranPendingCount > 0)
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping"></span>
                    <span class="text-lg font-bold text-amber-300">
                        {{ $pembayaranPendingCount }} Tagihan Menunggu
                    </span>
                </div>
                <a href="{{ route('admin.pembayaran.index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 text-xs font-bold border border-amber-400/30 transition">
                    <span>Verifikasi Sekarang</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
                @else
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                    <span class="text-base font-bold text-emerald-300">
                        Seluruh Kasir Bersih
                    </span>
                </div>
                <p class="text-xs text-slate-300">Semua bukti bayar telah selesai diproses.</p>
                @endif
            </div>

        </div>
    </div>

    {{-- ================= 4 METRIK UTAMA ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        {{-- Card 1: Dokter --}}
        <a href="{{ route('dokter.index') }}" class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Dokter Spesialis</span>
                    <span class="text-3xl font-black text-slate-800 mt-1 block">{{ $dokterCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#2d4499] flex items-center justify-center text-xl shadow-sm group-hover:scale-105 transition">
                    <i class="fas fa-user-doctor"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                <span>Staf medis terdaftar</span>
                <span class="font-bold text-[#2d4499] group-hover:translate-x-1 transition">&rarr;</span>
            </div>
        </a>

        {{-- Card 2: Pasien --}}
        <a href="{{ route('pasien.index') }}" class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-300 transition group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Pasien</span>
                    <span class="text-3xl font-black text-slate-800 mt-1 block">{{ $pasienCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm group-hover:scale-105 transition">
                    <i class="fas fa-hospital-user"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                <span>Pasien teregistrasi</span>
                <span class="font-bold text-amber-600 group-hover:translate-x-1 transition">&rarr;</span>
            </div>
        </a>

        {{-- Card 3: Poliklinik --}}
        <a href="{{ route('polis.index') }}" class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Poliklinik</span>
                    <span class="text-3xl font-black text-slate-800 mt-1 block">{{ $poliCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm group-hover:scale-105 transition">
                    <i class="fas fa-hospital"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                <span>Layanan spesialis aktif</span>
                <span class="font-bold text-emerald-600 group-hover:translate-x-1 transition">&rarr;</span>
            </div>
        </a>

        {{-- Card 4: Obat --}}
        <a href="{{ route('obat.index') }}" class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-purple-300 transition group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Master Obat</span>
                    <span class="text-3xl font-black text-slate-800 mt-1 block">{{ $obatCount }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-sm group-hover:scale-105 transition">
                    <i class="fas fa-pills"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                <span>Varian obat farmasi</span>
                <span class="font-bold text-purple-600 group-hover:translate-x-1 transition">&rarr;</span>
            </div>
        </a>

    </div>

    {{-- ================= TABEL PEMBAYARAN MENUNGGU VERIFIKASI ================= --}}
    <div class="card bg-white shadow-sm rounded-2xl border border-slate-200 mb-8">
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-lg text-slate-800">
                        Tagihan Pembayaran Menunggu Verifikasi
                    </h3>
                    @if($pembayaranPendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                        {{ $pembayaranPendingCount }} Baru
                    </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi yang baru saja diunggah oleh pasien atau notifikasi Midtrans.</p>
            </div>
            <a href="{{ route('admin.pembayaran.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline">
                Buka Kasir Lengkap &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Pasien</th>
                        <th class="px-6 py-4">Poli & Dokter</th>
                        <th class="px-6 py-4">Total Biaya</th>
                        <th class="px-6 py-4">Metode Bayar</th>
                        <th class="px-6 py-4">Bukti / Referensi</th>
                        <th class="px-6 py-4 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($pembayaranMenunggu as $bayar)
                    @php
                        $periksa = $bayar->periksa;
                        $dokter = optional($periksa->daftarPoli->jadwalPeriksa)->dokter;
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 block">{{ optional($bayar->pasien)->nama ?? 'Pasien' }}</span>
                            <span class="text-xs text-slate-400 font-mono">RM: {{ optional($bayar->pasien)->no_rm ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800 block text-xs">{{ optional($dokter->poli)->nama_poli ?? 'Poli' }}</span>
                            <span class="text-[11px] text-slate-500">dr. {{ optional($dokter)->nama ?? 'Dokter' }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-emerald-700 font-mono">
                            Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 inline-block">
                                {{ $bayar->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($bayar->bukti_bayar)
                                <a href="{{ asset('storage/' . $bayar->bukti_bayar) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                    Lihat Bukti
                                </a>
                            @elseif($bayar->order_id)
                                <span class="font-mono text-xs text-slate-500">{{ $bayar->order_id }}</span>
                            @else
                                <span class="text-xs text-slate-400 italic">Tidak ada lampiran</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="{{ route('admin.pembayaran.verifikasi', $bayar->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                                        <i class="fas fa-check text-[10px]"></i>
                                        <span>Setujui</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.pembayaran.tolak', $bayar->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold border border-red-200 transition cursor-pointer">
                                        <i class="fas fa-xmark text-[10px]"></i>
                                        <span>Tolak</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                            <i class="fas fa-circle-check text-3xl text-slate-300 mb-2 block"></i>
                            <span class="font-semibold block text-slate-600 text-sm">Tidak ada tagihan yang menunggu verifikasi.</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Semua transaksi pasien telah selesai diverifikasi atau lunas via sistem.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= GRID 2 KOLOM: POLIKLINIK & AKTIVITAS PEMERIKSAAN ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
        
        {{-- Kolom Kiri: Poliklinik & Peringatan Stok Obat --}}
        <div class="lg:col-span-5 space-y-8">
            
            {{-- Poliklinik Overview --}}
            <div class="card bg-white shadow-sm rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-base text-slate-800">Poliklinik Spesialis</h3>
                        <p class="text-xs text-slate-500">Kapasitas dokter pada setiap poli.</p>
                    </div>
                    <a href="{{ route('polis.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline">
                        Kelola Poli
                    </a>
                </div>

                <div class="space-y-3 mt-4">
                    @forelse($polis as $poli)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#2d4499] flex items-center justify-center text-xs font-bold">
                                <i class="fas fa-hospital text-xs"></i>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-slate-800 block">{{ $poli->nama_poli }}</span>
                                <span class="text-xs text-slate-400 line-clamp-1">{{ $poli->deskripsi ?: 'Pelayanan spesialis' }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs">
                            {{ $poli->dokters_count }} Dokter
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Belum ada poliklinik terdaftar.</div>
                    @endforelse
                </div>
            </div>

            {{-- Peringatan Stok Obat Menipis --}}
            <div class="card bg-white shadow-sm rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-base text-slate-800">Peringatan Stok Obat</h3>
                            @if($obatMenipis->count() > 0)
                            <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                Butuh Restock
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500">Obat dengan sisa stok di bawah 20 unit.</p>
                    </div>
                    <a href="{{ route('obat.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline">
                        Buka Farmasi
                    </a>
                </div>

                <div class="space-y-2.5 mt-4">
                    @forelse($obatMenipis as $o)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-red-50/50 border border-red-100">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-triangle-exclamation text-red-500 text-xs"></i>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">{{ $o->nama_obat }}</span>
                                <span class="text-[11px] text-slate-500">Kemasan: {{ $o->kemasan }}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-xs font-black bg-red-100 text-red-700">
                            Sisa {{ $o->stok }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        <i class="fas fa-circle-check text-emerald-500 text-base mb-1 block"></i>
                        Seluruh stok obat berada dalam batas aman.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Aktivitas Pemeriksaan Terbaru --}}
        <div class="lg:col-span-7 card bg-white shadow-sm rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Aktivitas Pemeriksaan Medis Terbaru</h3>
                    <p class="text-xs text-slate-500">Catatan pemeriksaan terkini di seluruh poliklinik.</p>
                </div>
                <span class="text-xs font-medium text-slate-400">Semua Poli</span>
            </div>

            <div class="space-y-3 mt-4">
                @forelse($riwayatPemeriksaanTerbaru as $periksa)
                @php
                    $pasien = optional($periksa->daftarPoli)->pasien;
                    $dokter = optional(optional($periksa->daftarPoli)->jadwalPeriksa)->dokter;
                @endphp
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-100/70 transition">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">
                                {{ $pasien->nama ?? 'Pasien' }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">(RM: {{ $pasien->no_rm ?? '-' }})</span>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-100 text-[#2d4499]">
                                {{ optional($dokter->poli)->nama_poli ?? 'Poli' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">
                            <strong class="text-slate-500">Dokter:</strong> dr. {{ $dokter->nama ?? 'Dokter' }}
                            &bull; <strong class="text-slate-500">Diagnosa:</strong> {{ $periksa->catatan ?: 'Pemeriksaan klinis' }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-xs font-bold text-slate-800 font-mono block">
                            Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                        </span>
                        <div class="mt-1">
                            @if(optional($periksa->pembayaran)->status === 'lunas')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">Lunas</span>
                            @elseif(optional($periksa->pembayaran)->status === 'menunggu_verifikasi')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Menunggu</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-600">Belum Bayar</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-slate-400 text-xs">
                    Belum ada riwayat aktivitas pemeriksaan medis.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ================= EKSPOR LAPORAN CEPAT ================= --}}
    <div class="card bg-slate-50 rounded-2xl border border-slate-200 p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Unduh Laporan Format Excel (.xlsx)</h4>
                <p class="text-xs text-slate-500 mt-0.5">Ekspor master data untuk keperluan pembukuan dan pelaporan klinik.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('dokter.export') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-2xs transition">
                    <i class="fas fa-file-excel text-emerald-600"></i>
                    <span>Export Dokter</span>
                </a>
                <a href="{{ route('pasien.export') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-2xs transition">
                    <i class="fas fa-file-excel text-emerald-600"></i>
                    <span>Export Pasien</span>
                </a>
                <a href="{{ route('obat.export') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-2xs transition">
                    <i class="fas fa-file-excel text-emerald-600"></i>
                    <span>Export Obat</span>
                </a>
            </div>
        </div>
    </div>

</x-layouts.app>