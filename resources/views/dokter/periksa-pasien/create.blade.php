<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Form Pemeriksaan Pasien
            </h2>
            <p class="text-xs text-slate-500">Input catatan pemeriksaan dan resep obat pasien.</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 max-w-3xl">
        <div class="card-body p-7">

            <form action="{{ route('periksa-pasien.store') }}" method="POST" id="formPeriksa">
                @csrf
                <input type="hidden" name="id_daftar_poli" value="{{ $id }}">
                <input type="hidden" name="obat_json" id="obat_json" value="[]">
                <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">

                {{-- Tanggal Periksa (Readonly) --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Tanggal Pemeriksaan
                        </span>
                    </label>
                    <input type="text" value="{{ now()->translatedFormat('d F Y, H:i') }} WIB"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-600 cursor-not-allowed"
                        readonly>
                </div>

                {{-- Catatan Dokter --}}
                <div class="form-control mb-6">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Catatan Pemeriksaan / Diagnosa
                        </span>
                    </label>
                    <textarea name="catatan" rows="4" placeholder="Tulis hasil diagnosa atau catatan resep dokter..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm resize-none focus:border-primary focus:outline-none @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>

                    @error('catatan')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Pilih Resep Obat --}}
                <div class="form-control mb-6">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Resepkan Obat (Bisa lebih dari satu) <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        @forelse($obats as $obat)
                        <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 bg-white hover:border-primary/50 cursor-pointer transition shadow-2xs">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                    class="checkbox-obat w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
                                    value="{{ $obat->id }}"
                                    data-harga="{{ $obat->harga }}"
                                    data-nama="{{ $obat->nama_obat }}">
                                <div>
                                    <div class="text-sm font-semibold text-slate-800">{{ $obat->nama_obat }}</div>
                                    <div class="text-xs text-slate-400">{{ $obat->kemasan ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="text-xs font-semibold text-emerald-600 font-mono">
                                Rp {{ number_format($obat->harga, 0, ',', '.') }}
                            </div>
                        </label>
                        @empty
                        <div class="col-span-2 text-center py-4 text-slate-400 text-sm">
                            Belum ada master data obat.
                        </div>
                        @endforelse
                    </div>

                    @error('obat_json')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Rincian Biaya --}}
                <div class="mb-7 p-5 rounded-xl border border-indigo-100 bg-indigo-50/40">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-receipt text-indigo-500"></i>
                        Rincian Biaya
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Biaya Jasa Dokter:</span>
                            <span class="font-medium text-slate-700">Rp 150.000</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Obat:</span>
                            <span class="font-medium text-slate-700 font-mono" id="labelSubtotalObat">Rp 0</span>
                        </div>
                        <div class="border-t border-indigo-200/60 pt-2 flex justify-between font-bold text-base text-slate-800">
                            <span>Total Biaya Pemeriksaan:</span>
                            <span class="text-indigo-600 font-mono" id="labelTotalBiaya">Rp 150.000</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('periksa-pasien.index') }}"
                        class="px-5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition">
                        <i class="fas fa-save mr-1.5"></i>
                        Simpan Pemeriksaan
                    </button>
                </div>

            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.checkbox-obat');
            const inputObatJson = document.getElementById('obat_json');
            const inputBiayaPeriksa = document.getElementById('biaya_periksa');
            const labelSubtotalObat = document.getElementById('labelSubtotalObat');
            const labelTotalBiaya = document.getElementById('labelTotalBiaya');
            const BIAYA_JASA_DOKTER = 150000;

            function formatRupiah(number) {
                return 'Rp ' + number.toLocaleString('id-ID');
            }

            function hitungTotal() {
                let selectedObatIds = [];
                let subtotalObat = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        selectedObatIds.push(parseInt(cb.value));
                        subtotalObat += parseInt(cb.getAttribute('data-harga')) || 0;
                    }
                });

                inputObatJson.value = JSON.stringify(selectedObatIds);
                inputBiayaPeriksa.value = subtotalObat;

                labelSubtotalObat.textContent = formatRupiah(subtotalObat);
                labelTotalBiaya.textContent = formatRupiah(subtotalObat + BIAYA_JASA_DOKTER);
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', hitungTotal);
            });

            hitungTotal();
        });
    </script>
    @endpush

</x-layouts.app>
