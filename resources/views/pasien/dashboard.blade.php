<x-layouts.app title="Dashboard Pasien">

    {{-- Header Dashboard --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Dashboard Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Selamat datang, <span class="font-semibold text-slate-700">{{ $user->nama }}</span>! Pantau antrean aktif dan jadwal dokter secara real-time.
            </p>
        </div>

        @if(!$antrianAktif)
        <a href="{{ route('pasien.daftar') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold rounded-xl text-sm shadow-sm transition hover:shadow cursor-pointer">
            <i class="fas fa-plus text-xs"></i>
            <span>Daftar Periksa Baru</span>
        </a>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if(session('message') || session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('message') ?? session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- 1. BANNER ANTRIAN AKTIF (CONTAINER LIVE)                  --}}
    {{-- ======================================================== --}}
    <div id="antrian-banner-container" class="mb-8">
        @if($antrianAktif)
        {{-- Banner Aktif Persis Mockup --}}
        <div class="relative overflow-hidden rounded-2xl shadow-lg transition-all duration-300"
            style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #4f46e5 100%);">
            
            {{-- Watermark Decorative Circles --}}
            <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full border-[18px] border-white/10 pointer-events-none"></div>
            <div class="absolute right-24 -bottom-16 w-52 h-52 rounded-full border-[14px] border-white/10 pointer-events-none"></div>

            <div class="relative z-10 p-6 sm:p-8">
                
                {{-- Banner Header --}}
                <div class="text-xs font-black uppercase tracking-widest text-white/80 mb-5">
                    ANTRIAN AKTIF ANDA
                </div>

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    
                    {{-- Left Side: Poli, Dokter, Jadwal --}}
                    <div class="space-y-4 max-w-xl">
                        <div>
                            <span class="text-xs text-blue-100 font-medium block">Poliklinik</span>
                            <span id="active-poli" class="text-xl sm:text-2xl font-black text-white tracking-tight block">
                                {{ optional($antrianAktif->jadwalPeriksa->dokter->poli)->nama_poli ?? 'Poliklinik' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs text-blue-100 font-medium block">Dokter</span>
                            <span id="active-dokter" class="text-lg sm:text-xl font-bold text-white block">
                                {{ optional($antrianAktif->jadwalPeriksa->dokter)->nama ?? 'Dokter Spesialis' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs text-blue-100 font-medium block">Jadwal Periksa</span>
                            <span id="active-jadwal" class="text-sm sm:text-base font-semibold text-white/95 block">
                                {{ $antrianAktif->jadwalPeriksa->hari }} ({{ date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_selesai)) }})
                            </span>
                        </div>
                    </div>

                    {{-- Right Side: Nomor Anda & Sedang Dilayani --}}
                    <div class="flex items-center gap-4 sm:gap-5 self-start lg:self-center">
                        
                        {{-- Box 1: Nomor Anda --}}
                        <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-5 text-center min-w-[125px] sm:min-w-[135px] shadow-sm">
                            <span class="text-xs font-semibold text-blue-100 block">Nomor Anda</span>
                            <span id="active-nomor-anda" class="text-4xl sm:text-5xl font-black text-white block mt-1 tracking-tight">
                                {{ $antrianAktif->no_antrian }}
                            </span>
                        </div>

                        {{-- Box 2: Sedang Dilayani (Live Update) --}}
                        <div class="bg-white rounded-2xl p-5 text-center min-w-[140px] sm:min-w-[150px] shadow-xl border border-white">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 block">
                                SEDANG DILAYANI
                            </span>
                            <span id="active-sedang-dilayani" class="text-4xl sm:text-5xl font-black text-[#2d4499] block mt-1 tracking-tight transition-all duration-300">
                                {{ $sedangDilayaniAktif }}
                            </span>
                            <div class="flex items-center justify-center gap-1.5 mt-2 text-[10px] font-bold text-slate-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                                <span>Live Update</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        @else
        {{-- Empty State: Jika Tidak Memiliki Antrian Aktif --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200 p-8 text-center">
            <div class="max-w-md mx-auto flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#2d4499] flex items-center justify-center text-2xl mb-4 border border-blue-100">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Tidak Ada Antrean Aktif</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Anda saat ini belum terdaftar pada antrean poli manapun. Silakan pilih poli dan jadwal dokter untuk mulai mendaftar pemeriksaan.
                </p>
                <a href="{{ route('pasien.daftar') }}"
                    class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold rounded-xl text-xs shadow-sm transition">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Daftar Antrean Sekarang</span>
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- 2. TABEL DAFTAR JADWAL POLIKLINIK DAN DOKTER             --}}
    {{-- ======================================================== --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            
            <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">
                        Jadwal Praktik Poliklinik & Dokter
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar jadwal dokter spesialis dan nomor antrean yang sedang dilayani saat ini.
                    </p>
                </div>
                
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-[#2d4499] text-xs font-semibold border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                    <span>Status Antrean Real-Time</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    
                    {{-- Head --}}
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">No</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">Sedang Dilayani</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($jadwals as $index => $jadwal)
                        <tr class="hover:bg-slate-50/80 transition" data-jadwal-row="{{ $jadwal->id }}">
                            
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-slate-400 font-medium text-xs">
                                {{ $index + 1 }}
                            </td>

                            {{-- Poli --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 text-[#2d4499] font-bold text-xs border border-blue-100">
                                    <i class="fas fa-hospital text-[10px]"></i>
                                    {{ optional($jadwal->dokter->poli)->nama_poli ?? '-' }}
                                </span>
                            </td>

                            {{-- Dokter --}}
                            <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                {{ optional($jadwal->dokter)->nama ?? 'Dokter' }}
                            </td>

                            {{-- Hari --}}
                            <td class="px-6 py-4 text-slate-700 font-semibold text-sm">
                                {{ $jadwal->hari }}
                            </td>

                            {{-- Jam Periksa --}}
                            <td class="px-6 py-4 text-slate-600 text-xs font-mono">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-md">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </span>
                            </td>

                            {{-- Nomor Sedang Dilayani (Live Badge) --}}
                            <td class="px-6 py-4 text-center">
                                <span data-jadwal-badge="{{ $jadwal->id }}"
                                    class="inline-flex items-center justify-center min-w-[50px] px-3 py-1 rounded-full text-xs font-black {{ $jadwal->nomor_sedang_dilayani !== '-' && $jadwal->nomor_sedang_dilayani !== 'Selesai' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-slate-100 text-slate-500' }} transition-all duration-300">
                                    {{ $jadwal->nomor_sedang_dilayani }}
                                </span>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-slate-400 text-sm">
                                <i class="fas fa-calendar-xmark text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada data jadwal praktik poliklinik.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusUrl = "{{ route('pasien.antrian.status') }}";

            // Helper untuk mengupdate badge Sedang Dilayani di setiap baris tabel
            function updateJadwalBadges(jadwals) {
                if (!jadwals || !Array.isArray(jadwals)) return;
                jadwals.forEach(item => {
                    const badge = document.querySelector(`[data-jadwal-badge="${item.id}"]`);
                    if (badge) {
                        const val = String(item.sedang_dilayani);
                        if (badge.textContent.trim() !== val) {
                            badge.textContent = val;
                            if (val !== '-' && val !== 'Selesai') {
                                badge.className = "inline-flex items-center justify-center min-w-[50px] px-3 py-1 rounded-full text-xs font-black bg-[#2d4499] text-white shadow-sm transition-all duration-300 scale-125";
                                setTimeout(() => {
                                    badge.classList.remove('scale-125');
                                }, 400);
                            } else {
                                badge.className = "inline-flex items-center justify-center min-w-[50px] px-3 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-500 transition-all duration-300";
                            }
                        }
                    }
                });
            }

            // Fungsi untuk update status antrian melalui API polling (sebagai pendukung & fallback)
            async function fetchLiveQueueStatus() {
                try {
                    const response = await fetch(statusUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    if (data.status !== 'success') return;

                    // 1. Update Banner Antrian Aktif
                    const sedangDilayaniEl = document.getElementById('active-sedang-dilayani');
                    if (data.has_active_queue && data.active_queue) {
                        if (sedangDilayaniEl) {
                            if (sedangDilayaniEl.textContent.trim() !== String(data.active_queue.sedang_dilayani)) {
                                sedangDilayaniEl.textContent = data.active_queue.sedang_dilayani;
                                sedangDilayaniEl.classList.add('scale-110', 'text-emerald-600');
                                setTimeout(() => {
                                    sedangDilayaniEl.classList.remove('scale-110', 'text-emerald-600');
                                }, 600);
                            }
                        }
                    } else {
                        // Jika pasien sebelumnya memiliki antrian tapi sekarang sudah selesai diperiksa
                        if (sedangDilayaniEl) {
                            const container = document.getElementById('antrian-banner-container');
                            if (container && !container.dataset.refreshed) {
                                container.dataset.refreshed = "true";
                                container.innerHTML = `
                                    <div class="card bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm p-6 text-center animate-fade-in">
                                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-xl mb-3">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <h3 class="text-base font-bold text-emerald-900">Pemeriksaan Anda Telah Selesai!</h3>
                                        <p class="text-xs text-emerald-700 mt-1">Terima kasih atas kunjungan Anda. Anda sekarang dapat mendaftar kembali jika membutuhkan pemeriksaan lain.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('pasien.daftar') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#2d4499] text-white rounded-xl text-xs font-semibold shadow-sm hover:bg-[#1e2d6b]">
                                                Daftar Pemeriksaan Baru
                                            </a>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    }

                    // 2. Update Badge di Setiap Baris Tabel Jadwal
                    updateJadwalBadges(data.jadwals);

                } catch (error) {
                    // Silently ignore network error
                }
            }

            // ========================================================
            // 3. REAL-TIME WEBSOCKET LISTENER DENGAN LARAVEL ECHO & REVERB
            // ========================================================
            function initEchoListener() {
                if (window.Echo) {
                    console.log('🔌 [Laravel Echo] Menghubungkan ke WebSocket channel: antrian-channel');

                    window.Echo.channel('antrian-channel')
                        .listen('.AntrianUpdated', (event) => {
                            console.log('⚡ [Real-Time WebSocket] Event Antrian Diterima:', event);

                            // Update live tabel antrian untuk semua jadwal poli
                            if (event.jadwals) {
                                updateJadwalBadges(event.jadwals);
                            }

                            // Update banner antrian aktif jika jadwal cocok
                            @if(isset($antrianAktif) && $antrianAktif)
                            const activeJadwalId = "{{ $antrianAktif->id_jadwal }}";
                            if (String(event.id_jadwal) === String(activeJadwalId) && event.nomor_sedang_dilayani) {
                                const sedangDilayaniEl = document.getElementById('active-sedang-dilayani');
                                if (sedangDilayaniEl) {
                                    sedangDilayaniEl.textContent = event.nomor_sedang_dilayani;
                                    sedangDilayaniEl.classList.add('scale-125', 'text-emerald-500');
                                    setTimeout(() => {
                                        sedangDilayaniEl.classList.remove('scale-125', 'text-emerald-500');
                                    }, 600);
                                }
                            }
                            @endif

                            // Sinkronkan status lengkap
                            fetchLiveQueueStatus();
                        });
                } else {
                    // Coba lagi setelah beberapa milidetik jika bundle Echo masih loading
                    setTimeout(initEchoListener, 300);
                }
            }

            initEchoListener();

            // Jalankan polling periodik sebagai fallback ketahanan koneksi
            setInterval(fetchLiveQueueStatus, 6000);
        });
    </script>
    @endpush

</x-layouts.app>