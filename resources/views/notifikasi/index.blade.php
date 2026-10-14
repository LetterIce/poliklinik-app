<x-layouts.app title="Semua Notifikasi">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Pusat Notifikasi
            </h2>
            <p class="text-sm text-slate-500 mt-1">Daftar pemberitahuan aktivitas akun, pemeriksaan, dan pembayaran Anda.</p>
        </div>

        @if($unreadCount > 0)
        <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                <i class="fas fa-check-double text-[#2d4499]"></i>
                <span>Tandai Semua Sudah Dibaca ({{ $unreadCount }})</span>
            </button>
        </form>
        @endif
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success mb-6 rounded-xl shadow-sm text-white">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Main Container Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
        
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-bell text-[#2d4499]"></i>
                <h3 class="font-bold text-slate-800 text-sm">Riwayat Notifikasi</h3>
            </div>
            <span class="text-xs text-slate-500 font-medium">
                Total: {{ $notifications->total() }} Notifikasi
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
            @endphp
            <div class="p-5 transition {{ $isUnread ? 'bg-blue-50/60 border-l-4 border-[#2d4499]' : 'hover:bg-slate-50/80 bg-white' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="w-10 h-10 rounded-xl {{ $isUnread ? 'bg-blue-100 text-[#2d4499]' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 text-base shadow-sm">
                            <i class="fas {{ $notif->data['icon'] ?? 'fa-bell' }}"></i>
                        </div>

                        {{-- Content --}}
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="text-sm font-bold {{ $isUnread ? 'text-[#2d4499]' : 'text-slate-800' }}">
                                    {{ $notif->data['title'] ?? 'Pemberitahuan' }}
                                </h4>
                                @if($isUnread)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-[#2d4499] border border-blue-200">
                                    BARU
                                </span>
                                @endif
                                <span class="text-xs text-slate-400 font-medium">
                                    • {{ $notif->created_at->translatedFormat('d M Y, H:i') }} ({{ $notif->created_at->diffForHumans() }})
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 leading-relaxed max-w-3xl">
                                {{ $notif->data['message'] ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        @if(isset($notif->data['action_url']))
                        <form action="{{ route('notifikasi.markAsRead', $notif->id) }}?redirect=1" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer">
                                <span>Lihat Detail</span>
                                <i class="fas fa-chevron-right text-[10px]"></i>
                            </button>
                        </form>
                        @endif

                        @if($isUnread)
                        <form action="{{ route('notifikasi.markAsRead', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-semibold shadow-sm transition cursor-pointer">
                                <i class="fas fa-check text-emerald-600 text-xs"></i>
                                <span>Tandai Dibaca</span>
                            </button>
                        </form>
                        @endif
                    </div>

                </div>
            </div>
            @empty
            <div class="p-16 text-center text-slate-400">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-300 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h4 class="text-base font-bold text-slate-700">Belum Ada Notifikasi</h4>
                <p class="text-xs text-slate-400 mt-1">Anda belum memiliki pemberitahuan riwayat pemeriksaan atau pembayaran saat ini.</p>
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $notifications->links() }}
        </div>
        @endif

    </div>

</x-layouts.app>
