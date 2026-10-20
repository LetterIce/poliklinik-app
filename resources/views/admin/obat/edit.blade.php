<x-layouts.app title="Edit Obat">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('obat.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Edit Obat
            </h2>
            <p class="text-xs text-slate-500">Perbarui rincian data obat.</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 max-w-2xl">
        <div class="card-body p-7">

            <form action="{{ route('obat.update', $obat->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nama Obat --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Nama Obat <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <input type="text" name="nama_obat" value="{{ old('nama_obat', $obat->nama_obat) }}"
                        placeholder="Contoh: Paracetamol 500mg"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('nama_obat') border-red-500 @enderror"
                        required>

                    @error('nama_obat')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Kemasan --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Kemasan
                        </span>
                    </label>

                    <input type="text" name="kemasan" value="{{ old('kemasan', $obat->kemasan) }}"
                        placeholder="Contoh: Strip @ 10 Tablet / Botol 60ml"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('kemasan') border-red-500 @enderror">

                    @error('kemasan')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Harga --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <input type="number" name="harga" value="{{ old('harga', $obat->harga) }}"
                        placeholder="Contoh: 15000" min="0"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('harga') border-red-500 @enderror"
                        required>

                    @error('harga')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Stok Obat --}}
                <div class="form-control mb-7">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Stok Obat <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <input type="number" name="stok" value="{{ old('stok', $obat->stok) }}"
                        placeholder="Contoh: 100" min="0"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('stok') border-red-500 @enderror"
                        required>

                    @error('stok')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Button --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('obat.index') }}"
                        class="px-5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition">
                        <i class="fas fa-save mr-1"></i>
                        Perbarui Data
                    </button>
                </div>

            </form>

        </div>
    </div>

</x-layouts.app>
