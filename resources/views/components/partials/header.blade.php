<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-3 sm:px-6 gap-2 sm:gap-4 sticky top-0 z-30 shadow-sm">

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" aria-label="Toggle Sidebar" class="btn btn-square btn-ghost lg:hidden text-slate-700 hover:bg-slate-100">
        <i class="fas fa-bars text-lg"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm truncate">
            <span class="text-base-content/50 hidden xs:inline">Poliklinik</span>
            <i class="fas fa-chevron-right text-[10px] text-base-content/30 hidden xs:inline"></i>
            <span class="font-semibold text-base-content truncate">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Fullscreen (Hidden on small mobile) --}}
    <button onclick="toggleFullscreen()" aria-label="Toggle Fullscreen" class="hidden sm:inline-flex btn btn-square btn-ghost text-slate-600 hover:bg-slate-100">
        <i id="fsIcon" class="fas fa-expand w-4 h-4"></i>
    </button>

    @auth
    @php
        $unreadNotifCount = auth()->user()->unreadNotifications->count();
        $recentNotifs = auth()->user()->notifications()->latest()->take(6)->get();
    @endphp

    {{-- Notification Dropdown --}}
    <div id="headerNotificationDropdown" class="dropdown dropdown-end relative">
        <button tabindex="0" role="button" aria-label="Notifikasi" class="relative p-2 sm:p-2.5 rounded-xl hover:bg-slate-100 text-slate-600 transition flex items-center justify-center cursor-pointer">
            <i class="fas fa-bell text-base sm:text-lg"></i>
            @if($unreadNotifCount > 0)
            <span class="absolute top-1 right-1 sm:top-1.5 sm:right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
            </span>
            @endif
        </button>

        <div tabindex="0" class="dropdown-content z-[100] menu p-0 shadow-2xl bg-base-100 rounded-2xl border border-slate-200 w-[calc(100vw-1.5rem)] max-w-sm sm:w-96 mt-2 overflow-hidden right-0">
            {{-- Header Dropdown --}}
            <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm text-slate-800">Notifikasi</span>
                    @if($unreadNotifCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#2d4499]">
                        {{ $unreadNotifCount }} Baru
                    </span>
                    @endif
                </div>

                @if($unreadNotifCount > 0)
                <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-[#2d4499] hover:underline cursor-pointer">
                        Tandai semua dibaca
                    </button>
                </form>
                @endif
            </div>

            {{-- List Notifikasi --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                @forelse($recentNotifs as $notif)
                @php
                    $isUnread = is_null($notif->read_at);
                @endphp
                <div class="p-3.5 transition {{ $isUnread ? 'bg-blue-50/70 border-l-4 border-[#2d4499]' : 'hover:bg-slate-50' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $isUnread ? 'bg-blue-100 text-[#2d4499]' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 text-xs">
                            <i class="fas {{ $notif->data['icon'] ?? 'fa-bell' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="text-xs font-bold {{ $isUnread ? 'text-[#2d4499]' : 'text-slate-800' }}">
                                    {{ $notif->data['title'] ?? 'Notifikasi' }}
                                </span>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $notif->data['message'] ?? '-' }}
                            </p>

                            <div class="flex items-center justify-between mt-2 pt-1">
                                @if(isset($notif->data['action_url']))
                                <form action="{{ route('notifikasi.markAsRead', $notif->id) }}?redirect=1" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold text-[#2d4499] hover:underline inline-flex items-center gap-1 cursor-pointer">
                                        <span>Lihat Detail</span>
                                        <i class="fas fa-chevron-right text-[9px]"></i>
                                    </button>
                                </form>
                                @else
                                <span></span>
                                @endif

                                @if($isUnread)
                                <form action="{{ route('notifikasi.markAsRead', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-medium text-slate-400 hover:text-slate-700 cursor-pointer">
                                        Tandai dibaca
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-bell-slash text-2xl mb-2 text-slate-300 block"></i>
                    <span class="text-xs">Belum ada notifikasi</span>
                </div>
                @endforelse
            </div>

            {{-- Footer Dropdown --}}
            <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                <a href="{{ route('notifikasi.index') }}" class="text-xs font-bold text-[#2d4499] hover:underline block py-1">
                    Lihat Semua Notifikasi
                </a>
            </div>
        </div>
    </div>
    @endauth

    {{-- User Profile Dropdown --}}
    <div class="dropdown dropdown-end">
        <button tabindex="0" role="button" class="flex items-center gap-3 p-1 rounded-xl hover:bg-slate-100 transition cursor-pointer">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold leading-tight text-slate-800">
                    {{ auth()->user()->nama ?? 'Pengguna' }}
                </div>
                <div class="text-xs text-slate-400 leading-tight capitalize">
                    {{ auth()->user()->role ?? 'User' }}
                </div>
            </div>

            <div class="avatar">
                <div class="w-10 h-10 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-bold shadow-sm">
                    <span class="text-sm font-semibold leading-none">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                    </span>
                </div>
            </div>
            <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden sm:block"></i>
        </button>

        <ul tabindex="0" class="dropdown-content z-[100] menu p-2 shadow-xl bg-base-100 rounded-2xl border border-slate-200 w-56 mt-2">
            <li class="menu-title px-4 py-2 border-b border-slate-100">
                <span class="text-xs font-semibold text-slate-400">Akun Pengguna</span>
                <span class="text-xs font-bold text-slate-800 truncate block">{{ auth()->user()->email ?? '-' }}</span>
            </li>
            <li>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-[#2d4499] rounded-xl transition">
                    <i class="fas fa-user-gear w-4 text-center"></i>
                    <span>Profil & Password</span>
                </a>
            </li>
            <li class="border-t border-slate-100 my-1"></li>
            <li>
                <form method="POST" action="/logout" class="p-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-xl transition">
                        <i class="fas fa-right-from-bracket w-4 text-center"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

</header>

<script>
    function toggleFullscreen() {
    const icon = document.getElementById('fsIcon');

    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        icon.classList.remove('fa-expand');
        icon.classList.add('fa-compress');
    } else {
        document.exitFullscreen();
        icon.classList.remove('fa-compress');
        icon.classList.add('fa-expand');
    }
}
</script>