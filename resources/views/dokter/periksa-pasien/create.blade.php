<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Form Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('periksa-pasien.store') }}" method="POST" id="formPeriksa">
                @csrf
                <input type="hidden" name="id_daftar_poli" value="{{ $id }}">
                <input type="hidden" name="obat_json" id="obat_json" value="[]">
                <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">

                {{-- Biaya Jasa Dokter --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Biaya Jasa Dokter / Pemeriksaan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-2.5 text-sm font-semibold text-slate-400">Rp</span>
                        <input type="number" name="biaya_dokter" id="biaya_dokter" min="0" step="1000"
                            value="{{ old('biaya_dokter', $biayaDokterDefault ?? 150000) }}"
                            class="w-full pl-12 pr-4 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:border-primary focus:outline-none bg-white @error('biaya_dokter') border-red-500 @enderror"
                            placeholder="150000" required>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Tarif dasar dokter otomatis dimuat dan dapat disesuaikan jika ada tindakan khusus.</p>
                    @error('biaya_dokter')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilih Obat --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Pilih Obat <span class="text-red-500">*</span>
                    </label>

                    <select id="select_obat"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none bg-white">
                        <option value="">-- Pilih Obat --</option>
                        @foreach($obats as $obat)
                        @php
                            $isOutOfStock = ($obat->stok ?? 0) <= 0;
                        @endphp
                        <option value="{{ $obat->id }}" 
                            data-nama="{{ $obat->nama_obat }}" 
                            data-harga="{{ $obat->harga }}" 
                            data-kemasan="{{ $obat->kemasan }}"
                            data-stok="{{ $obat->stok ?? 0 }}"
                            {{ $isOutOfStock ? 'disabled' : '' }}>
                            {{ $obat->nama_obat }} {{ $obat->kemasan ? '('.$obat->kemasan.')' : '' }} - Rp {{ number_format($obat->harga, 0, ',', '.') }} {{ $isOutOfStock ? '[STOK HABIS]' : '(Sisa Stok: ' . ($obat->stok ?? 0) . ')' }}
                        </option>
                        @endforeach
                    </select>

                    @error('obat_json')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Obat Terpilih --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Obat Terpilih
                    </label>
                    <div id="container_obat_terpilih" class="min-h-[46px] p-2.5 rounded-lg border border-slate-200 bg-slate-50/50 flex flex-wrap gap-2 items-center">
                        <span id="text_kosong" class="text-xs text-slate-400 italic">Belum ada obat yang dipilih</span>
                    </div>
                </div>

                {{-- Rincian & Total Tagihan --}}
                <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Biaya Dokter:</span>
                        <span id="labelBiayaDokter" class="font-mono font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Subtotal Obat:</span>
                        <span id="labelBiayaObat" class="font-mono font-semibold">Rp 0</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800">Total Biaya Pemeriksaan:</span>
                        <span id="labelTotalHarga" class="text-base font-black text-primary font-mono">Rp 0</span>
                    </div>
                </div>

                {{-- Catatan (Opsional) --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Catatan <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="catatan" rows="4" placeholder="Masukkan catatan..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm resize-none focus:border-primary focus:outline-none @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition inline-flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>

                    <a href="{{ route('periksa-pasien.index') }}"
                        class="px-6 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectObat = document.getElementById('select_obat');
            const containerObatTerpilih = document.getElementById('container_obat_terpilih');
            const textKosong = document.getElementById('text_kosong');
            const inputObatJson = document.getElementById('obat_json');
            const inputBiayaPeriksa = document.getElementById('biaya_periksa');
            const labelTotalHarga = document.getElementById('labelTotalHarga');

            const inputBiayaDokter = document.getElementById('biaya_dokter');
            const labelBiayaDokter = document.getElementById('labelBiayaDokter');
            const labelBiayaObat = document.getElementById('labelBiayaObat');

            let selectedObats = []; // { id, nama, harga }

            function formatRupiah(number) {
                return 'Rp ' + number.toLocaleString('id-ID');
            }

            function updateUI() {
                containerObatTerpilih.innerHTML = '';

                if (selectedObats.length === 0) {
                    containerObatTerpilih.appendChild(textKosong);
                } else {
                    selectedObats.forEach((item, index) => {
                        const badge = document.createElement('div');
                        badge.className = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-700 shadow-xs';
                        badge.innerHTML = `
                            <span>${item.nama} (${formatRupiah(item.harga)})</span>
                            <button type="button" class="text-slate-400 hover:text-red-500 transition focus:outline-none" data-index="${index}">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        `;

                        badge.querySelector('button').addEventListener('click', function () {
                            hapusObat(index);
                        });

                        containerObatTerpilih.appendChild(badge);
                    });
                }

                const totalObat = selectedObats.reduce((sum, item) => sum + item.harga, 0);
                const biayaDokter = parseInt(inputBiayaDokter.value) || 0;
                const totalBiaya = biayaDokter + totalObat;
                const obatIds = selectedObats.map(item => item.id);

                inputObatJson.value = JSON.stringify(obatIds);
                inputBiayaPeriksa.value = totalBiaya;
                labelBiayaDokter.textContent = formatRupiah(biayaDokter);
                labelBiayaObat.textContent = formatRupiah(totalObat);
                labelTotalHarga.textContent = formatRupiah(totalBiaya);
            }

            if (inputBiayaDokter) {
                inputBiayaDokter.addEventListener('input', updateUI);
            }

            // Inisialisasi hitungan awal
            updateUI();

            function hapusObat(index) {
                selectedObats.splice(index, 1);
                updateUI();
            }

            selectObat.addEventListener('change', function () {
                const selectedOption = selectObat.options[selectObat.selectedIndex];
                if (!selectedOption.value) return;

                const id = parseInt(selectedOption.value);
                const nama = selectedOption.getAttribute('data-nama');
                const harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;

                // Cek jika sudah pernah dipilih
                if (!selectedObats.some(item => item.id === id)) {
                    selectedObats.push({ id, nama, harga });
                    updateUI();
                }

                // Reset pilihan dropdown
                selectObat.value = '';
            });

            updateUI();
        });
    </script>
    @endpush

</x-layouts.app>
