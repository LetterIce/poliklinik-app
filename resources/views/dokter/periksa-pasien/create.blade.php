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

                {{-- Pilih Obat --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Pilih Obat <span class="text-red-500">*</span>
                    </label>

                    <select id="select_obat"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none bg-white">
                        <option value="">-- Pilih Obat --</option>
                        @foreach($obats as $obat)
                        <option value="{{ $obat->id }}" data-nama="{{ $obat->nama_obat }}" data-harga="{{ $obat->harga }}" data-kemasan="{{ $obat->kemasan }}">
                            {{ $obat->nama_obat }} {{ $obat->kemasan ? '('.$obat->kemasan.')' : '' }} - Rp {{ number_format($obat->harga, 0, ',', '.') }}
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

                {{-- Total Harga --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Total Harga
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-800 font-mono" id="labelTotalHarga">
                        Rp 0
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
                const obatIds = selectedObats.map(item => item.id);

                inputObatJson.value = JSON.stringify(obatIds);
                inputBiayaPeriksa.value = totalObat;
                labelTotalHarga.textContent = formatRupiah(totalObat);
            }

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
