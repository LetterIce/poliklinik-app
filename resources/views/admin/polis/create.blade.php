<x-layouts.app title="Tambah Poli">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('polis.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Tambah Poli
            </h2>
            <p class="text-xs text-slate-500">Masukkan nama dan keterangan poli baru.</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 max-w-2xl">
        <div class="card-body p-7">

            <form action="{{ route('polis.store') }}" method="POST">
                @csrf

                {{-- Nama Poli --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Nama Poli <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <input type="text" name="nama_poli" value="{{ old('nama_poli') }}"
                        placeholder="Masukkan nama poli..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('nama_poli') border-red-500 @enderror"
                        required>

                    @error('nama_poli')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="form-control mb-6">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Keterangan <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <textarea name="keterangan" rows="4" placeholder="Masukkan keterangan poli..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm resize-none focus:border-primary focus:outline-none @error('keterangan') border-red-500 @enderror"
                        required>{{ old('keterangan') }}</textarea>

                    @error('keterangan')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('polis.index') }}"
                        class="px-5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition">
                        <i class="fas fa-save mr-1"></i>
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

</x-layouts.app>