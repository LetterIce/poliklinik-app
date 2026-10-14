<x-layouts.app title="Tambah Jadwal Periksa">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal-periksa.index') }}"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Tambah Jadwal Periksa
            </h2>
            <p class="text-xs text-slate-500">Tentukan hari dan jam operasional praktik Anda.</p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 max-w-2xl">
        <div class="card-body p-7">

            <form action="{{ route('jadwal-periksa.store') }}" method="POST">
                @csrf

                {{-- Hari --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">
                            Hari Praktik <span class="text-red-500">*</span>
                        </span>
                    </label>

                    <select name="hari" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('hari') border-red-500 @enderror" required>
                        <option value="">Pilih Hari...</option>
                        @foreach($hariList as $hari)
                        <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>
                            {{ $hari }}
                        </option>
                        @endforeach
                    </select>

                    @error('hari')
                    <label class="label pt-1">
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Grid Jam --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-7">

                    {{-- Jam Mulai --}}
                    <div class="form-control">
                        <label class="label pb-1">
                            <span class="text-sm font-semibold text-gray-700">
                                Jam Mulai <span class="text-red-500">*</span>
                            </span>
                        </label>

                        <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('jam_mulai') border-red-500 @enderror"
                            required>

                        @error('jam_mulai')
                        <label class="label pt-1">
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    {{-- Jam Selesai --}}
                    <div class="form-control">
                        <label class="label pb-1">
                            <span class="text-sm font-semibold text-gray-700">
                                Jam Selesai <span class="text-red-500">*</span>
                            </span>
                        </label>

                        <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-primary focus:outline-none @error('jam_selesai') border-red-500 @enderror"
                            required>

                        @error('jam_selesai')
                        <label class="label pt-1">
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                </div>

                {{-- Button --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('jadwal-periksa.index') }}"
                        class="px-5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm shadow-sm transition">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Jadwal
                    </button>
                </div>

            </form>

        </div>
    </div>

</x-layouts.app>
