<x-layouts.app title="Data Obat">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Data Obat
            </h2>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar obat, kemasan, dan harga untuk pelayanan medis.</p>
        </div>

        <a href="{{ route('obat.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 
                  text-white rounded-xl text-sm font-semibold shadow-sm transition">
            <i class="fas fa-plus text-sm"></i>
            Tambah Obat
        </a>
    </div>

    {{-- Alert Success / Message --}}
    @if(session('message'))
    <div class="alert alert-success mb-4 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="alert alert-error mb-4 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">No</th>
                            <th class="px-6 py-4">Nama Obat</th>
                            <th class="px-6 py-4">Kemasan</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody>
                        @forelse($obats as $index => $obat)
                        <tr class="hover:bg-slate-50 transition border-b border-slate-100">
                            <td class="px-6 py-4 text-center text-slate-500 font-medium">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $obat->nama_obat }}
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $obat->kemasan ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-semibold text-emerald-600">
                                Rp {{ number_format($obat->harga, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('obat.edit', $obat->id) }}" class="inline-flex items-center gap-1 px-3 py-2 
                                        bg-amber-500 hover:bg-amber-600 
                                        text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        <i class="fas fa-pen-to-square text-xs"></i>
                                        Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('obat.destroy', $obat->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus obat ini?')"
                                            class="inline-flex items-center gap-1 px-3 py-2 
                                            bg-red-500 hover:bg-red-600 
                                            text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            <i class="fas fa-trash text-xs"></i>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-16 text-slate-400">
                                <i class="fas fa-pills text-3xl mb-3 block text-slate-300"></i>
                                Belum ada data obat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>
