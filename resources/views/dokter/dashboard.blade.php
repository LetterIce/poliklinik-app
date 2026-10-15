<x-layouts.app title="Dashboard Dokter">

    {{-- ================= HEADER DASHBOARD ================= --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="text-2xl font-bold text-slate-800">
                    Dashboard Dokter
                </h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Selamat bertugas, <span class="font-semibold text-slate-700">dr. {{ $dokter->nama }}</span>! Kelola antrean pasien dan pemeriksaan medis hari ini.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('jadwal-periksa.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-sm border border-slate-200 shadow-sm transition">
                <i class="fas fa-calendar-days text-[#2d4499]"></i>
                <span>Jadwal Praktik</span>
            </a>
            <a href="{{ route('periksa-pasien.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold rounded-xl text-sm shadow-sm transition hover:shadow">
                <i class="fas fa-stethoscope"></i>
                <span>Ruang Periksa</span>
            </a>
        </div>
    </div>

    {{-- Alert Notifikasi Session --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- ================= STATS CARDS ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        {{-- Card 1: Antrean Menunggu --}}
        <div class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Antrean Menunggu</span>
                    <span class="text-3xl font-black text-slate-800 mt-1 block">{{ $antreanMenunggu->count() }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-users-line"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">
                @if($antreanMenunggu->count() > 0)
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span class="text-amber-600 font-semibold">Ada pasien menunggu giliran</span>
                @else
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-emerald-600 font-semibold">Tidak ada antrean tertunda</span>
                @endif
            </div>
        </div>

        {{-- Card 2: Selesai Hari Ini --}}
        <div class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Selesai Hari Ini</span>
                    <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ $selesaiHariIni }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-500">
                Pasien diperiksa pada <span class="font-semibold">{{ $hariIni }}</span>
            </div>
        </div>

        {{-- Card 3: Total Pasien Ditangani --}}
        <div class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Ditangani</span>
                    <span class="text-3xl font-black text-[#2d4499] mt-1 block">{{ $totalPasienDitangani }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-[#2d4499] flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-hospital-user"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-500">
                Akumulasi seluruh rekam medis
            </div>
        </div>

        {{-- Card 4: Jadwal Hari Ini --}}
        <div class="card bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Jadwal {{ $hariIni }}</span>
                    @if($jadwalHariIni)
                        <span class="text-lg font-black text-slate-800 mt-1 block">
                            {{ date('H:i', strtotime($jadwalHariIni->jam_mulai)) }} - {{ date('H:i', strtotime($jadwalHariIni->jam_selesai)) }}
                        </span>
                    @else
                        <span class="text-lg font-black text-slate-400 mt-1 block">Tidak Ada</span>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-2xl {{ $jadwalHariIni ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="mt-3 text-xs">
                @if($jadwalHariIni)
                    <span class="text-emerald-600 font-semibold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Praktik Hari Ini
                    </span>
                @else
                    <span class="text-slate-400 font-medium">Jadwal libur hari ini</span>
                @endif
            </div>
        </div>

    </div>

    {{-- ================= BANNER PANGGILAN ANTREAN HARI INI ================= --}}
    @if($jadwalHariIni)
    <div class="relative overflow-hidden rounded-2xl shadow-lg mb-8 p-6 sm:p-8 text-white transition-all"
         style="background: linear-gradient(135deg, #1e293b 0%, #2d4499 100%);">
        
        <div class="absolute -right-10 -bottom-10 w-52 h-52 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute right-36 -top-10 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <h3 class="text-2xl font-black tracking-tight text-white">
                    Poli {{ optional($dokter->poli)->nama_poli ?? 'Umum' }}
                </h3>
                <p class="text-sm text-slate-300">
                    Jadwal Praktik Hari Ini: <strong class="text-white">{{ $hariIni }} ({{ date('H:i', strtotime($jadwalHariIni->jam_mulai)) }} - {{ date('H:i', strtotime($jadwalHariIni->jam_selesai)) }} WIB)</strong>
                </p>
            </div>

            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-md border border-white/20 p-4 sm:p-5 rounded-2xl">
                <div class="text-center px-2">
                    <span class="text-xs uppercase tracking-wider text-indigo-200 font-bold block">Antrean Terdepan</span>
                    <span class="text-4xl sm:text-5xl font-mono font-black text-white block mt-1">
                        {{ $nomorSedangDipanggil !== '-' ? '#' . $nomorSedangDipanggil : '-' }}
                    </span>
                </div>
                @if($antreanMenunggu->where('id_jadwal', $jadwalHariIni->id)->count() > 0)
                @php
                    $nextPatient = $antreanMenunggu->where('id_jadwal', $jadwalHariIni->id)->first();
                @endphp
                <div class="border-l border-white/20 pl-4 space-y-2">
                    <form method="POST" action="{{ route('periksa-pasien.panggil', $nextPatient->id) }}">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-md transition cursor-pointer">
                            <i class="fas fa-bullhorn text-xs"></i>
                            <span>Panggil Pasien</span>
                        </button>
                    </form>
                    <a href="{{ route('periksa-pasien.create', $nextPatient->id) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white hover:bg-slate-100 text-[#2d4499] rounded-xl text-xs font-bold shadow-md transition">
                        <i class="fas fa-notes-medical text-xs"></i>
                        <span>Mulai Periksa</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ================= TABEL ANTREAN PASIEN MENUNGGU ================= --}}
    <div class="card bg-white shadow-sm rounded-2xl border border-slate-200 mb-8">
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-lg text-slate-800">
                    Daftar Antrean Pasien Menunggu
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pasien yang telah mendaftar dan siap diperiksa di ruang tindakan.</p>
            </div>
            <a href="{{ route('periksa-pasien.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline">
                Buka Menu Periksa Pasien &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-center w-20">Antrean</th>
                        <th class="px-6 py-4">Pasien</th>
                        <th class="px-6 py-4">Keluhan</th>
                        <th class="px-6 py-4">Jadwal Periksa</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($antreanMenunggu as $antrean)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl font-mono font-black text-sm bg-indigo-50 text-[#2d4499] border border-indigo-200">
                                {{ $antrean->no_antrian }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ optional($antrean->pasien)->nama ?? 'Pasien' }}</div>
                            <span class="text-xs text-slate-400 font-mono">RM: {{ optional($antrean->pasien)->no_rm ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <span class="text-slate-600 text-xs line-clamp-2">{{ $antrean->keluhan ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold text-slate-700 block">
                                {{ optional($antrean->jadwalPeriksa)->hari ?? '-' }}
                            </span>
                            <span class="text-[11px] text-slate-400">
                                {{ date('H:i', strtotime(optional($antrean->jadwalPeriksa)->jam_mulai)) }} - {{ date('H:i', strtotime(optional($antrean->jadwalPeriksa)->jam_selesai)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('periksa-pasien.panggil', $antrean->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition cursor-pointer"
                                        title="Panggil nomor antrean via WebSocket">
                                        <i class="fas fa-bullhorn text-xs"></i>
                                        <span>Panggil</span>
                                    </button>
                                </form>
                                <a href="{{ route('periksa-pasien.create', $antrean->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#2d4499] hover:bg-[#1e2d6b] text-white text-xs font-bold shadow-sm transition">
                                    <i class="fas fa-stethoscope text-xs"></i>
                                    <span>Periksa</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <i class="fas fa-circle-check text-4xl text-slate-300 mb-3 block"></i>
                            <span class="font-semibold block text-slate-600">Semua antrean pasien sudah selesai diperiksa!</span>
                            <span class="text-xs text-slate-400 mt-1 block">Tidak ada pasien yang sedang menunggu di ruang tunggu.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= GRID 2 KOLOM: JADWAL & RIWAYAT TERAKHIR ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Kolom Kiri: Jadwal Praktik Mingguan --}}
        <div class="lg:col-span-5 card bg-white shadow-sm rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Jadwal Praktik Dokter</h3>
                    <p class="text-xs text-slate-500">Jadwal konsultasi rawat jalan Anda.</p>
                </div>
                <a href="{{ route('jadwal-periksa.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline">
                    Kelola
                </a>
            </div>

            <div class="space-y-3 mt-4">
                @forelse($jadwals as $j)
                <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $j->hari === $hariIni ? 'bg-indigo-50/50 border-indigo-200' : 'bg-slate-50 border-slate-100' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $j->hari === $hariIni ? 'bg-[#2d4499] text-white' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center text-xs font-bold">
                            {{ substr($j->hari, 0, 2) }}
                        </div>
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">{{ $j->hari }}</span>
                            <span class="text-xs text-slate-500">
                                {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }} WIB
                            </span>
                        </div>
                    </div>
                    <div>
                        @if($j->hari === $hariIni)
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-700">Praktik Hari Ini</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600">Terjadwal</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    Belum ada jadwal praktik yang didaftarkan.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom Kanan: 5 Pasien Terakhir Diperiksa --}}
        <div class="lg:col-span-7 card bg-white shadow-sm rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-base text-slate-800">Riwayat Pemeriksaan Terakhir</h3>
                    <p class="text-xs text-slate-500">5 pasien terakhir yang telah selesai ditangani.</p>
                </div>
                <a href="{{ route('riwayat-pasien.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline">
                    Semua Riwayat
                </a>
            </div>

            <div class="space-y-3.5 mt-4">
                @forelse($riwayatTerakhir as $riwayat)
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">
                                {{ optional($riwayat->daftarPoli->pasien)->nama ?? 'Pasien' }}
                            </span>
                            <span class="text-xs font-mono text-slate-400">
                                (RM: {{ optional($riwayat->daftarPoli->pasien)->no_rm ?? '-' }})
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 line-clamp-1">
                            <strong class="text-slate-500">Catatan:</strong> {{ $riwayat->catatan ?: 'Pemeriksaan rutin' }}
                        </p>
                        @if($riwayat->detailPeriksas->count() > 0)
                        <div class="flex flex-wrap gap-1 pt-1">
                            @foreach($riwayat->detailPeriksas as $detail)
                                <span class="px-1.5 py-0.2 rounded text-[10px] bg-blue-100 text-[#2d4499] font-medium">
                                    {{ optional($detail->obat)->nama_obat }}
                                </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="text-right self-end sm:self-center shrink-0">
                        <span class="text-xs font-bold text-emerald-700 block">
                            Rp {{ number_format($riwayat->biaya_periksa, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">
                            {{ date('d/m/Y H:i', strtotime($riwayat->tgl_periksa)) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    Belum ada riwayat pemeriksaan pasien.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</x-layouts.app>