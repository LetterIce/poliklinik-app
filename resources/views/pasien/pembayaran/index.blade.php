<x-layouts.app title="Riwayat Pembayaran">

    {{-- Script Midtrans Snap Sandbox / Production --}}
    @php
        $snapUrl = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = config('services.midtrans.client_key');
    @endphp
    <script type="text/javascript" src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pembayaran & Tagihan
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftar tagihan pemeriksaan medis dan status verifikasi pembayaran Anda.</p>
        </div>

        {{-- Badge Informasi Layanan --}}
        <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#2d4499]">
            <i class="fas fa-shield-halved text-emerald-600"></i>
            <span>Mendukung Pembayaran Non-Tunai (QRIS & Virtual Account Bank)</span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
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
                            <th class="px-6 py-4 w-14 text-center">No</th>
                            <th class="px-6 py-4">Poli & Dokter</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Total Biaya</th>
                            <th class="px-6 py-4 text-center">Status Pembayaran</th>
                            <th class="px-6 py-4 text-right">Metode / Aksi Bayar</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="divide-y divide-slate-100">
                        @forelse($periksas as $index => $periksa)
                        @php
                            $pembayaran = $periksa->pembayaran;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            {{-- No --}}
                            <td class="px-6 py-4 text-center text-slate-400 font-medium text-xs">
                                {{ $index + 1 }}
                            </td>

                            {{-- Poli & Dokter --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ optional($periksa->daftarPoli->jadwalPeriksa->dokter->poli)->nama_poli ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Dokter: {{ optional($periksa->daftarPoli->jadwalPeriksa->dokter)->nama ?? '-' }}
                                </div>
                            </td>

                            {{-- Tanggal Periksa --}}
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                <div class="font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('H:i') }} WIB
                                </div>
                            </td>

                            {{-- Total Biaya --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 font-mono text-sm">
                                    Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status Pembayaran --}}
                            <td class="px-6 py-4 text-center">
                                @if(!$pembayaran)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-exclamation-circle text-xs"></i>
                                        Belum Bayar
                                    </span>
                                @elseif($pembayaran->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-clock text-xs"></i>
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($pembayaran->status === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Lunas
                                    </span>
                                @elseif($pembayaran->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle text-xs"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if(!$pembayaran || $pembayaran->status === 'ditolak')
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Tombol Bayar Digital --}}
                                        <button type="button"
                                            id="btn-midtrans-{{ $periksa->id }}"
                                            onclick="payWithMidtrans({{ $periksa->id }})"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-[#2d4499] to-indigo-600 hover:from-[#1e2d6b] hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                            <i class="fas fa-qrcode text-xs text-emerald-300"></i>
                                            <span>Bayar Instan (QRIS / VA)</span>
                                        </button>

                                        {{-- Opsi Upload Bukti Manual --}}
                                        <a href="{{ route('pasien.pembayaran.create', $periksa->id) }}"
                                            title="Upload Bukti Transfer Manual"
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold shadow-sm transition">
                                            <i class="fas fa-upload text-xs"></i>
                                            <span>Manual</span>
                                        </a>
                                    </div>
                                @elseif($pembayaran->status === 'menunggu_verifikasi')
                                    <div class="flex items-center justify-end gap-2">
                                        @if($pembayaran->snap_token)
                                        <button type="button"
                                            id="btn-midtrans-{{ $periksa->id }}"
                                            onclick="payWithMidtrans({{ $periksa->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-[#2d4499] hover:bg-blue-100 rounded-lg text-xs font-semibold border border-blue-200 transition">
                                            <i class="fas fa-credit-card text-xs"></i>
                                            <span>Lanjut Bayar</span>
                                        </button>
                                        @endif
                                        @if($pembayaran->bukti_bayar)
                                        <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition">
                                            <i class="fas fa-file-image text-xs"></i>
                                            Lihat Bukti
                                        </a>
                                        @endif
                                    </div>
                                @else
                                    {{-- Lunas --}}
                                    <div class="inline-flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-check-circle text-xs"></i>
                                            <span>{{ $pembayaran->metode_pembayaran ?? 'Lunas' }}</span>
                                        </span>
                                    </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-slate-400">
                                <i class="fas fa-file-invoice text-3xl mb-3 block text-slate-300"></i>
                                Belum ada riwayat tagihan pemeriksaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    {{-- Script Integrasi Midtrans Snap --}}
    <script>
        function payWithMidtrans(periksaId) {
            const btn = document.getElementById('btn-midtrans-' + periksaId);
            const originalContent = btn ? btn.innerHTML : '';

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Menyiapkan...';
            }

            fetch('/pasien/pembayaran/' + periksaId + '/snap-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => null);
                if (!res.ok) {
                    throw new Error((data && data.message) ? data.message : ('Gagal menghubungi server (Status: ' + res.status + ')'));
                }
                return data;
            })
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }

                if (data && data.status === 'success' && data.snap_token) {
                    if (typeof window.snap === 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Memuat Komponen Pembayaran',
                            text: 'Modul Midtrans Snap belum selesai dimuat. Silakan refresh halaman dan coba lagi.',
                            confirmButtonColor: '#2d4499'
                        });
                        return;
                    }

                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Pembayaran Anda telah diterima. Sistem sedang memperbarui status transaksi Anda.',
                                confirmButtonColor: '#2d4499'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onPending: function(result) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                text: 'Silakan selesaikan pembayaran sesuai instruksi QRIS / Virtual Account yang dipilih.',
                                confirmButtonColor: '#2d4499'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onError: function(result) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Tidak Berhasil',
                                text: 'Transaksi dibatalkan atau gagal diproses oleh penyedia pembayaran.',
                                confirmButtonColor: '#2d4499'
                            });
                        },
                        onClose: function() {
                            // Pasien menutup pop-up tanpa menyelesaikan pembayaran
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memproses Pembayaran',
                        text: (data && data.message) ? data.message : 'Gagal memuat pembayaran Midtrans. Silakan coba metode transfer manual.',
                        confirmButtonColor: '#2d4499'
                    });
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kendala Koneksi',
                    text: err.message || 'Terjadi kendala koneksi ke server. Silakan coba kembali.',
                    confirmButtonColor: '#2d4499'
                });
            });
        }
    </script>

</x-layouts.app>
