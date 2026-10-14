<x-layouts.app title="Upload Bukti Pembayaran">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.pembayaran.index') }}"
            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 shadow-sm transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Upload Bukti Pembayaran
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">Kirim foto bukti transfer pembayaran hasil pemeriksaan medis Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

        {{-- Left Form: Upload --}}
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
                <div class="card-body p-7">

                    <form action="{{ route('pasien.pembayaran.store', $periksa->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Metode Pembayaran --}}
                        <div class="form-control mb-5">
                            <label class="label pb-1">
                                <span class="text-sm font-semibold text-slate-700">Metode Pembayaran Transfer <span class="text-red-500">*</span></span>
                            </label>
                            <select name="metode_pembayaran" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#2d4499] focus:outline-none">
                                <option value="Transfer Bank BCA" {{ old('metode_pembayaran') == 'Transfer Bank BCA' ? 'selected' : '' }}>Transfer Bank BCA (123-456-7890 a.n Poliklinik Sehat)</option>
                                <option value="Transfer Bank Mandiri" {{ old('metode_pembayaran') == 'Transfer Bank Mandiri' ? 'selected' : '' }}>Transfer Bank Mandiri (987-654-3210 a.n Poliklinik Sehat)</option>
                                <option value="Transfer Bank BRI" {{ old('metode_pembayaran') == 'Transfer Bank BRI' ? 'selected' : '' }}>Transfer Bank BRI (555-666-777 a.n Poliklinik Sehat)</option>
                                <option value="QRIS / E-Wallet" {{ old('metode_pembayaran') == 'QRIS / E-Wallet' ? 'selected' : '' }}>QRIS / GoPay / OVO / Dana</option>
                            </select>
                        </div>

                        {{-- File Bukti Pembayaran dengan Live Preview --}}
                        <div class="form-control mb-6">
                            <label class="label pb-1">
                                <span class="text-sm font-semibold text-slate-700">Foto Bukti Transfer / Pembayaran <span class="text-red-500">*</span></span>
                            </label>

                            {{-- Dropzone / Upload Area --}}
                            <div class="mt-1">
                                <input type="file" id="bukti_bayar_input" name="bukti_bayar" accept="image/jpeg,image/png,image/jpg,application/pdf" required
                                    class="w-full file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#2d4499] hover:file:bg-blue-100 border border-slate-300 rounded-xl text-sm p-1.5 focus:border-[#2d4499] focus:outline-none @error('bukti_bayar') border-red-500 @enderror">
                                <p class="text-xs text-slate-400 mt-1.5">Format file: JPG, PNG, atau PDF. Ukuran berkas maksimal 3 MB.</p>
                            </div>

                            @error('bukti_bayar')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror

                            {{-- Container Live Preview --}}
                            <div id="imagePreviewContainer" class="mt-4 hidden">
                                <span class="text-xs font-semibold text-slate-600 block mb-2">Pratinjau Bukti Transfer:</span>
                                <div class="relative inline-block border-2 border-dashed border-blue-200 bg-slate-50 rounded-xl p-2 max-w-sm">
                                    <img id="imagePreview" src="#" alt="Pratinjau Bukti" class="max-h-60 rounded-lg object-contain">
                                    <div id="pdfNotice" class="hidden p-4 text-center text-xs text-slate-600">
                                        <i class="fas fa-file-pdf text-red-500 text-3xl mb-1 block"></i>
                                        Dokumen PDF dipilih
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('pasien.pembayaran.index') }}"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-semibold text-sm shadow-sm transition inline-flex items-center gap-2">
                                <i class="fas fa-cloud-arrow-up text-sm"></i>
                                <span>Upload & Kirim Bukti</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- Right: Rincian Tagihan --}}
        <div>
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fas fa-receipt text-[#2d4499]"></i>
                    <span>Rincian Tagihan Pemeriksaan</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Poli Tujuan:</span>
                        <span class="font-bold text-slate-800">{{ optional($periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dokter Pemeriksa:</span>
                        <span class="font-bold text-slate-800">{{ optional($periksa->daftarPoli->jadwalPeriksa->dokter)->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Periksa:</span>
                        <span class="font-mono text-slate-700">{{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jasa Dokter:</span>
                        <span class="font-mono text-slate-700 font-semibold">Rp 150.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Resep Obat ({{ $periksa->detailPeriksas ? $periksa->detailPeriksas->count() : 0 }}):</span>
                        <span class="font-mono text-slate-700 font-semibold">
                            Rp {{ number_format(max(0, $periksa->biaya_periksa - 150000), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-200 pt-4">
                    <span class="text-xs text-slate-500 uppercase font-bold block mb-1">Total Tagihan Harus Dibayar</span>
                    <div class="text-2xl font-black text-[#2d4499] font-mono">
                        Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                    </div>
                </div>

                <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-[11px] text-blue-900 leading-relaxed">
                    <i class="fas fa-info-circle mr-1 text-[#2d4499]"></i>
                    Setelah mengunggah bukti bayar, notifikasi otomatis terkirim ke dokter pemeriksa dan status akan dikonfirmasi lunas oleh Admin poliklinik.
                </div>
            </div>
        </div>

    </div>

    {{-- Script Live Preview --}}
    <script>
        const fileInput = document.getElementById('bukti_bayar_input');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const imgPreview = document.getElementById('imagePreview');
        const pdfNotice = document.getElementById('pdfNotice');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                previewContainer.classList.remove('hidden');
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        imgPreview.src = evt.target.result;
                        imgPreview.classList.remove('hidden');
                        pdfNotice.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    imgPreview.classList.add('hidden');
                    pdfNotice.classList.remove('hidden');
                }
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    </script>

</x-layouts.app>
