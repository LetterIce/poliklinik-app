<x-layouts.app title="Edit Poli">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('polis.index') }}" class="flex items-center justify-center w-9 h-9 
                  rounded-lg bg-slate-100 text-slate-500 
                  hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>

        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Edit Poli
            </h2>
            <p class="text-xs text-slate-500">Perbarui data poli.</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 max-w-2xl">
        <div class="card-body p-7">

            <form action="{{ route('polis.update', $poli->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nama Poli --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Nama Poli <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="nama_poli" value="{{ old('nama_poli', $poli->nama_poli) }}"
                        placeholder="Masukkan nama poli..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('nama_poli') border-red-500 @enderror"
                        required>

                    @error('nama_poli')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Keterangan <span class="text-red-500">*</span>
                    </label>

                    <textarea name="keterangan" rows="4" placeholder="Masukkan keterangan poli..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm resize-none focus:border-primary focus:outline-none @error('keterangan') border-red-500 @enderror"
                        required>{{ old('keterangan', $poli->keterangan) }}</textarea>

                    @error('keterangan')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Button --}}
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