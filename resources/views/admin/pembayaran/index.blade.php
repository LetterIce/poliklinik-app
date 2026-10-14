<x-layouts.app title="Verifikasi Pembayaran Pasien">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-800">
                    Verifikasi Pembayaran Pasien
                </h2>
                @if(isset($countMenunggu) && $countMenunggu > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                    <i class="fas fa-bell"></i>
                    {{ $countMenunggu }} Perlu Verifikasi
                </span>
                @endif
            </div>
            <p class="text-sm text-slate-500 mt-1">Daftar tagihan pemeriksaan dan verifikasi bukti transfer pembayaran dari pasien.</p>
        </div>

        {{-- Filter Status --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pembayaran.index') }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ !$status ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'menunggu_verifikasi']) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'menunggu_verifikasi' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                <span>Menunggu Verifikasi</span>
                @if(isset($countMenunggu) && $countMenunggu > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'menunggu_verifikasi' ? 'bg-white text-[#2d4499]' : 'bg-amber-500 text-white' }} font-bold">
                    {{ $countMenunggu }}
                </span>
                @endif
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'lunas']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'lunas' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Lunas
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'ditolak']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'ditolak' ? 'bg-[#2d4499] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Ditolak
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('message'))
    <div class="alert alert-info mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-info-circle"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Table Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">No</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Pemeriksaan / Poli</th>
                            <th class="px-6 py-4">Jumlah Bayar</th>
                            <th class="px-6 py-4">Bukti Transfer</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-slate-400 font-medium text-xs">
                                {{ $pembayarans->firstItem() + $index }}
                            </td>

                            {{-- Pasien --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ $item->pasien->nama ?? 'Pasien' }}
                                </div>
                                @if(optional($item->pasien)->no_rm)
                                <div class="text-xs text-slate-400 font-mono mt-0.5">
                                    RM: {{ $item->pasien->no_rm }}
                                </div>
                                @endif
                            </td>

                            {{-- Pemeriksaan / Poli --}}
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700 text-sm">
                                    {{ optional($item->periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Dokter: {{ optional($item->periksa->daftarPoli->jadwalPeriksa->dokter)->nama ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->tgl_bayar)->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            </td>

                            {{-- Jumlah Bayar --}}
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-slate-800 text-sm">
                                    Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->metode_pembayaran }}
                                </div>
                            </td>

                            {{-- Bukti Transfer --}}
                            <td class="px-6 py-4">
                                @if($item->bukti_bayar)
                                    @php
                                        $fileExt = strtolower(pathinfo($item->bukti_bayar, PATHINFO_EXTENSION));
                                        $fileUrl = asset('storage/' . $item->bukti_bayar);
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        {{-- Tombol Buka Modal Preview --}}
                                        <button type="button"
                                            onclick="openModalPreview('{{ $fileUrl }}', '{{ $fileExt }}', '{{ addslashes($item->pasien->nama ?? 'Pasien') }}', 'Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}', '{{ $item->metode_pembayaran }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-[#2d4499] hover:bg-blue-100 rounded-lg text-xs font-semibold border border-blue-200 transition">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span>Lihat Bukti</span>
                                        </button>
                                        <a href="{{ $fileUrl }}" target="_blank" title="Buka di tab baru"
                                            class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                                            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak ada file</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($item->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-clock text-xs"></i>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($item->status === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Lunas
                                    </span>
                                @elseif($item->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle text-xs"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($item->status === 'menunggu_verifikasi')
                                    <form action="{{ route('admin.pembayaran.verifikasi', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Konfirmasi pembayaran pasien {{ addslashes($item->pasien->nama ?? 'ini') }} sebagai LUNAS? Notifikasi akan otomatis terkirim ke pasien.')"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                            <i class="fas fa-check text-xs"></i>
                                            <span>Konfirmasi Lunas</span>
                                        </button>
                                    </form>
                                @elseif($item->status === 'lunas')
                                    <span class="text-xs font-semibold text-emerald-600 flex items-center justify-end gap-1">
                                        <i class="fas fa-circle-check text-xs"></i>
                                        Telah Diverifikasi
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Selesai diproses</span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <i class="fas fa-receipt text-3xl mb-3 block text-slate-300"></i>
                                Tidak ada data tagihan / pembayaran ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $pembayarans->links() }}
            </div>
            @endif

        </div>
    </div>

    {{-- Modal Preview Bukti Bayar --}}
    <dialog id="modal_preview_bukti" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box max-w-2xl bg-white rounded-2xl p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800 text-base" id="modalPasienNama">Bukti Pembayaran Pasien</h3>
                    <p class="text-xs text-slate-500 mt-0.5" id="modalPaymentInfo">Rincian bukti transfer</p>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-slate-600">✕</button>
                </form>
            </div>

            <div class="my-4 flex items-center justify-center bg-slate-50 rounded-xl p-3 border border-slate-100 min-h-[250px] max-h-[500px] overflow-auto">
                <img id="modalPreviewImage" src="" alt="Bukti Transfer" class="max-w-full max-h-[460px] object-contain rounded-lg shadow-sm hidden">
                <iframe id="modalPreviewPdf" src="" class="w-full h-[450px] rounded-lg hidden"></iframe>
                <div id="modalPreviewLoading" class="text-slate-400 text-sm flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i> Memuat berkas...
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <a id="modalOpenExternal" href="#" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    <i class="fas fa-external-link-alt text-xs"></i>
                    Buka Ukuran Penuh
                </a>
                <form method="dialog">
                    <button class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-semibold transition">
                        Tutup
                    </button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openModalPreview(url, ext, pasienNama, jumlahBayar, metode) {
            const modal = document.getElementById('modal_preview_bukti');
            const img = document.getElementById('modalPreviewImage');
            const pdf = document.getElementById('modalPreviewPdf');
            const loading = document.getElementById('modalPreviewLoading');
            const pasienEl = document.getElementById('modalPasienNama');
            const infoEl = document.getElementById('modalPaymentInfo');
            const externalLink = document.getElementById('modalOpenExternal');

            pasienEl.innerText = 'Bukti Pembayaran: ' + pasienNama;
            infoEl.innerText = 'Total: ' + jumlahBayar + ' • ' + metode;
            externalLink.href = url;

            img.classList.add('hidden');
            pdf.classList.add('hidden');
            loading.classList.remove('hidden');

            if (ext === 'pdf') {
                pdf.src = url;
                pdf.onload = () => {
                    loading.classList.add('hidden');
                    pdf.classList.remove('hidden');
                };
            } else {
                img.src = url;
                img.onload = () => {
                    loading.classList.add('hidden');
                    img.classList.remove('hidden');
                };
                img.onerror = () => {
                    loading.innerHTML = '<span class="text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat gambar</span>';
                };
            }

            modal.showModal();
        }
    </script>

</x-layouts.app>
