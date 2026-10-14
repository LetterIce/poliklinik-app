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

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 max-w-2xl">
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
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition">
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
