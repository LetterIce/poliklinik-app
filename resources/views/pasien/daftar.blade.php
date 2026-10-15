<x-layouts.app title="Pendaftaran Poli">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Pendaftaran Poli
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftarkan diri Anda untuk pemeriksaan ke dokter spesialis poli.</p>
        </div>
    </div>

    {{-- Alert Success / Message --}}
    @if(session('message') || session('success'))
    <div class="alert alert-success mb-5 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('message') ?? session('success') }}</span>
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="alert alert-error mb-5 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(isset($antrianAktif) && $antrianAktif)
    {{-- Banner Antrean Aktif (Mencegah Daftar Poli Kedua) --}}
    <div class="card bg-amber-50 border border-amber-200 rounded-2xl shadow-sm mb-6 w-full p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-clock"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-amber-900 text-base">Anda Masih Memiliki Antrean Aktif</h3>
                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                    Sesuai ketentuan, pasien tidak dapat mendaftar ke dua poli sekaligus. Anda hanya dapat mendaftar kembali setelah pemeriksaan saat ini selesai dilayani oleh dokter.
                </p>

                <div class="mt-4 p-4 bg-white/80 rounded-xl border border-amber-200/60 grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Poli Tujuan:</span>
                        <span class="font-bold text-slate-800 text-sm">{{ optional($antrianAktif->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Dokter Pemeriksa:</span>
                        <span class="font-bold text-slate-800 text-sm">{{ optional($antrianAktif->jadwalPeriksa->dokter)->nama ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Jadwal:</span>
                        <span class="font-semibold text-slate-700">{{ $antrianAktif->jadwalPeriksa->hari }} ({{ date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_selesai)) }})</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Nomor Antrean Anda:</span>
                        <span class="font-black text-amber-700 text-sm">#{{ $antrianAktif->no_antrian }}</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('pasien.dashboard') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold rounded-xl text-xs shadow-sm transition">
                        <i class="fas fa-gauge-high text-xs"></i>
                        <span>Pantau Antrean di Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 w-full {{ (isset($antrianAktif) && $antrianAktif) ? 'opacity-60 pointer-events-none select-none' : '' }}">
        <div class="card-body p-7">

            <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                {{-- No. Rekam Medis (Readonly) --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-slate-700">
                            Nomor Rekam Medis (No. RM)
                        </span>
                    </label>
                    <input type="text" value="{{ $user->no_rm ?? 'Belum ada No RM' }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm font-mono text-slate-700 cursor-not-allowed"
                        readonly>
                </div>

                {{-- Pilih Poli --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-slate-700">
                            Pilih Poli <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <select name="id_poli" id="id_poli"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('id_poli') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Poli Tujuan --</option>
                        @foreach($polis as $poli)
                        <option value="{{ $poli->id }}" {{ old('id_poli') == $poli->id ? 'selected' : '' }}>
                            {{ $poli->nama_poli }}
                        </option>
                        @endforeach
                    </select>

                    @error('id_poli')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Pilih Jadwal & Dokter --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-slate-700">
                            Pilih Jadwal Dokter <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <select name="id_jadwal" id="id_jadwal"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('id_jadwal') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Dokter & Jadwal Praktik --</option>
                        @foreach($jadwals as $jadwal)
                        @php
                            $poliId = optional($jadwal->dokter)->id_poli;
                            $namaDokter = optional($jadwal->dokter)->nama ?? 'Dokter';
                            $jamMulai = date('H:i', strtotime($jadwal->jam_mulai));
                            $jamSelesai = date('H:i', strtotime($jadwal->jam_selesai));
                        @endphp
                        <option value="{{ $jadwal->id }}"
                            data-poli="{{ $poliId }}"
                            {{ old('id_jadwal') == $jadwal->id ? 'selected' : '' }}>
                            {{ $namaDokter }} - {{ $jadwal->hari }} ({{ $jamMulai }} - {{ $jamSelesai }})
                        </option>
                        @endforeach
                    </select>

                    @error('id_jadwal')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Keluhan --}}
                <div class="form-control mb-7">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-slate-700">
                            Keluhan Pasien
                        </span>
                    </label>

                    <textarea name="keluhan" rows="4" placeholder="Tuliskan keluhan atau gejala yang Anda rasakan..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm resize-none focus:border-primary focus:outline-none @error('keluhan') border-red-500 @enderror">{{ old('keluhan') }}</textarea>

                    @error('keluhan')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold text-sm shadow-sm transition">
                        <i class="fas fa-paper-plane mr-1.5"></i>
                        Daftar Sekarang
                    </button>
                </div>

            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectPoli = document.getElementById('id_poli');
            const selectJadwal = document.getElementById('id_jadwal');
            const allJadwalOptions = Array.from(selectJadwal.querySelectorAll('option'));

            function filterJadwal() {
                const selectedPoliId = selectPoli.value;

                selectJadwal.innerHTML = '';
                
                let count = 0;
                allJadwalOptions.forEach(option => {
                    if (!option.value) {
                        selectJadwal.appendChild(option.cloneNode(true));
                        return;
                    }

                    const poliId = option.getAttribute('data-poli');
                    if (selectedPoliId && String(poliId) === String(selectedPoliId)) {
                        selectJadwal.appendChild(option.cloneNode(true));
                        count++;
                    }
                });

                if (selectedPoliId && count === 0) {
                    const emptyOpt = document.createElement('option');
                    emptyOpt.value = '';
                    emptyOpt.textContent = '-- Belum ada jadwal dokter untuk poli ini --';
                    emptyOpt.disabled = true;
                    emptyOpt.selected = true;
                    selectJadwal.appendChild(emptyOpt);
                }
            }

            selectPoli.addEventListener('change', function () {
                filterJadwal();
                selectJadwal.value = '';
            });

            if (selectPoli.value) {
                filterJadwal();
            }
        });
    </script>
    @endpush

</x-layouts.app>
