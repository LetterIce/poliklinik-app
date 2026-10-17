<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik</title>

    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .hero-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#2d449912 1px, transparent 1px), radial-gradient(#2d449912 1px, #f8fafc 1px);
            background-size: 36px 36px;
            background-position: 0 0, 18px 18px;
        }

        /* Kartun Bergerak & Floating Animations */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(1.2deg); }
        }
        @keyframes floatReverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(12px) rotate(-1.5deg); }
        }
        @keyframes floatFast {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-7px) scale(1.03); }
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.45; }
            50% { transform: scale(1.15); opacity: 0.75; }
        }
        @keyframes twinkle {
            0%, 100% { transform: scale(0.7) rotate(0deg); opacity: 0.3; }
            50% { transform: scale(1.2) rotate(45deg); opacity: 1; }
        }
        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            14% { transform: scale(1.2); }
            28% { transform: scale(1); }
            42% { transform: scale(1.2); }
            70% { transform: scale(1); }
        }

        .anim-float-slow {
            animation: floatSlow 4.5s ease-in-out infinite;
        }
        .anim-float-reverse {
            animation: floatReverse 5.2s ease-in-out infinite;
        }
        .anim-float-fast {
            animation: floatFast 3s ease-in-out infinite;
        }
        .anim-pulse-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }
        .anim-twinkle {
            animation: twinkle 2.2s ease-in-out infinite;
        }
        .anim-heartbeat {
            animation: heartBeat 2s ease-in-out infinite;
        }
        .mascot-bounce:active {
            transform: scale(0.95);
        }

        /* Scroll Reveal Soft Animation */
        .reveal-soft {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.85s cubic-bezier(0.16, 1, 0.3, 1), transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-soft.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-[#2d4499] selection:text-white">

    {{-- ================= NAVBAR ================= --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="/" class="flex items-center gap-3.5 group">

                <div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 block leading-tight">
                        Poli<span class="text-[#2d4499]">Klinik</span>
                    </span>
                    <span class="text-[11px] font-semibold text-slate-400 tracking-wider uppercase block">peduli keluarga</span>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#beranda" class="hover:text-[#2d4499] transition">Beranda</a>
                <a href="#layanan" class="hover:text-[#2d4499] transition">Layanan Spesialis</a>
                <a href="#fasilitas" class="hover:text-[#2d4499] transition">Fasilitas</a>
                <a href="#alur" class="hover:text-[#2d4499] transition">Alur Pasien</a>
                <a href="#jadwal" class="hover:text-[#2d4499] transition">Jadwal Praktik</a>
            </nav>

            {{-- Action Buttons & Mobile Hamburger --}}
            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    @php
                        $role = auth()->user()->role;
                        $dashboardRoute = route('login');
                        if ($role === 'admin') $dashboardRoute = route('admin.dashboard');
                        elseif ($role === 'dokter') $dashboardRoute = route('dokter.dashboard');
                        elseif ($role === 'pasien') $dashboardRoute = route('pasien.dashboard');
                    @endphp
                    <a href="{{ $dashboardRoute }}"
                        class="inline-flex items-center gap-2 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white text-xs sm:text-sm font-bold shadow-md shadow-indigo-900/10 transition-all hover:scale-102">
                        <i class="fas fa-user-check text-xs"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-slate-700 hover:text-[#2d4499] hover:bg-slate-100 text-sm font-bold transition">
                        <i class="fas fa-right-to-bracket text-xs"></i>
                        <span>Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white text-xs sm:text-sm font-bold shadow-md shadow-indigo-900/10 transition-all hover:scale-102">
                        <i class="fas fa-calendar-plus text-xs"></i>
                        <span>Daftar Berobat</span>
                    </a>
                @endauth

                {{-- Mobile Menu Hamburger Button --}}
                <button type="button" id="mobileMenuBtn" aria-label="Menu Navigasi" class="lg:hidden p-2 rounded-xl text-slate-700 hover:text-[#2d4499] hover:bg-slate-100 transition focus:outline-none">
                    <i class="fas fa-bars text-xl" id="mobileMenuIcon"></i>
                </button>
            </div>

        </div>

        {{-- Mobile Drawer Menu --}}
        <div id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 bg-white/98 backdrop-blur-md px-4 pt-3 pb-6 shadow-xl space-y-1.5 transition-all">
            <a href="#beranda" class="mobile-nav-link block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:text-[#2d4499] hover:bg-slate-50 transition">
                <i class="fas fa-house w-5 text-[#2d4499] mr-2 text-xs"></i> Beranda
            </a>
            <a href="#layanan" class="mobile-nav-link block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:text-[#2d4499] hover:bg-slate-50 transition">
                <i class="fas fa-stethoscope w-5 text-[#2d4499] mr-2 text-xs"></i> Layanan Spesialis
            </a>
            <a href="#fasilitas" class="mobile-nav-link block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:text-[#2d4499] hover:bg-slate-50 transition">
                <i class="fas fa-hospital w-5 text-[#2d4499] mr-2 text-xs"></i> Fasilitas
            </a>
            <a href="#alur" class="mobile-nav-link block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:text-[#2d4499] hover:bg-slate-50 transition">
                <i class="fas fa-shoe-prints w-5 text-[#2d4499] mr-2 text-xs"></i> Alur Pasien
            </a>
            <a href="#jadwal" class="mobile-nav-link block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:text-[#2d4499] hover:bg-slate-50 transition">
                <i class="fas fa-calendar-check w-5 text-[#2d4499] mr-2 text-xs"></i> Jadwal Praktik
            </a>
            <a href="#kontak" class="mobile-nav-link block px-3 py-2.5 rounded-xl font-semibold text-slate-700 hover:text-[#2d4499] hover:bg-slate-50 transition">
                <i class="fas fa-headset w-5 text-[#2d4499] mr-2 text-xs"></i> Kontak & Lokasi
            </a>

            @guest
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl border border-slate-200 font-bold text-sm text-slate-700 hover:bg-slate-50 transition">
                    <i class="fas fa-right-to-bracket text-xs mr-1.5"></i> Masuk Akun
                </a>
            </div>
            @endguest
        </div>
    </header>

    {{-- ================= HERO SECTION ================= --}}
    <section id="beranda" class="hero-pattern min-h-[calc(100vh-5rem)] flex flex-col justify-center py-8 sm:py-12 lg:py-16 overflow-hidden border-b border-slate-200 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                {{-- Left Text --}}
                <div class="lg:col-span-7 space-y-5 sm:space-y-6 text-center lg:text-left">

                    {{-- Main Headline --}}
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.2]">
                        Merawat Anda Sepenuh Hati, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#2d4499] via-indigo-600 to-blue-600">Seperti Keluarga Sendiri.</span>
                    </h1>

                    {{-- Subheadline Medis --}}
                    <p class="text-sm sm:text-base lg:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Kami memahami betapa berharganya waktu dan kesehatan Anda. Hadir untuk memberikan perawatan terbaik tanpa proses yang rumit.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-3.5 pt-1 sm:pt-2">
                        @auth
                            <a href="{{ route('pasien.daftar') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 sm:px-7 py-3 sm:py-3.5 rounded-2xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-bold text-sm sm:text-base shadow-lg shadow-indigo-900/20 transition-all hover:scale-102">
                                <i class="fas fa-calendar-check"></i>
                                <span>Pendaftaran Pemeriksaan</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 sm:px-7 py-3 sm:py-3.5 rounded-2xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-bold text-sm sm:text-base shadow-lg shadow-indigo-900/20 transition-all hover:scale-102">
                                <i class="fas fa-user-plus"></i>
                                <span>Daftar Pasien Baru</span>
                            </a>
                        @endauth
                        <a href="#jadwal"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm sm:text-base border border-slate-200 shadow-sm transition">
                            <i class="fas fa-user-doctor text-[#2d4499]"></i>
                            <span>Jadwal Dokter Spesialis</span>
                        </a>
                    </div>

                    {{-- Data Kepercayaan Pasien --}}
                    <div class="pt-5 sm:pt-6 border-t border-slate-200/60 grid grid-cols-3 gap-2 sm:gap-6 text-center lg:text-left">
                        <div class="px-1">
                            <span class="block text-xl sm:text-2xl font-black text-slate-900">{{ $poliCount ?? 4 }}+</span>
                            <span class="text-[10px] sm:text-xs text-slate-500 font-medium">Poliklinik Spesialis</span>
                        </div>
                        <div class="px-1 border-x border-slate-200/60 lg:border-none">
                            <span class="block text-xl sm:text-2xl font-black text-slate-900">{{ $dokterCount ?? 5 }}+</span>
                            <span class="text-[10px] sm:text-xs text-slate-500 font-medium">Dokter Spesialis Ahli</span>
                        </div>
                        <div class="px-1">
                            <span class="block text-xl sm:text-2xl font-black text-[#2d4499]">4.9 / 5.0</span>
                            <span class="text-[10px] sm:text-xs text-slate-500 font-medium">Kepuasan Pasien</span>
                        </div>
                    </div>

                </div>

                {{-- Right Visual: Animasi Kartun Bergerak & Floating Badges --}}
                <div class="lg:col-span-5 relative py-2 sm:py-6">
                    <div class="relative mx-auto max-w-[340px] sm:max-w-[440px] flex items-center justify-center min-h-[340px] sm:min-h-[460px]">
                        
                        {{-- Ambient Animated Glows --}}
                        <div class="absolute w-80 h-80 bg-gradient-to-tr from-blue-300/40 via-indigo-300/30 to-purple-300/40 rounded-full blur-3xl anim-pulse-glow -z-10 pointer-events-none"></div>
                        <div class="absolute -bottom-6 -right-6 w-56 h-56 bg-emerald-200/40 rounded-full blur-2xl anim-float-slow -z-10 pointer-events-none" style="animation-duration: 7s;"></div>

                        {{-- Floating Micro Elements (Sparkles, Heart, Pill) --}}
                        <div class="absolute top-2 left-4 sm:left-6 text-lg sm:text-xl anim-twinkle select-none pointer-events-none z-20">✨</div>
                        <div class="absolute top-1/4 right-1 sm:-right-2 text-xl sm:text-2xl anim-heartbeat select-none pointer-events-none z-20">❤️</div>
                        <div class="absolute bottom-16 left-1 sm:-left-3 text-xl sm:text-2xl anim-float-fast select-none pointer-events-none z-20">💊</div>
                        <div class="absolute bottom-6 right-6 sm:right-8 text-lg sm:text-xl anim-twinkle select-none pointer-events-none z-20" style="animation-delay: -1.2s;">✨</div>

                        {{-- Floating Speech Bubble --}}
                        <div class="absolute -top-4 sm:-top-7 right-2 sm:right-10 bg-white/95 backdrop-blur-md px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-1.5 sm:gap-2 anim-float-fast z-30">
                            <span class="text-xs sm:text-sm">👋</span>
                            <span class="text-[11px] sm:text-xs font-bold text-slate-700">Yuk, jaga kesehatanmu!</span>
                            {{-- Pointer tail pointing down-left toward doctor --}}
                            <div class="absolute -bottom-1.5 left-5 sm:left-6 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-white rotate-45 border-r border-b border-slate-100"></div>
                        </div>

                        {{-- Main Cartoon Character (Animated) --}}
                        <div class="relative z-10 w-full flex items-center justify-center p-2">
                            <img src="{{ asset('images/hero-mascot-original.png') }}"
                                 alt="Karakter Medis Kartun Poliklinik"
                                 id="heroMascot"
                                 class="w-full max-w-[340px] sm:max-w-[400px] object-contain drop-shadow-2xl anim-float-slow transition-transform duration-300 hover:scale-105 active:scale-95 cursor-pointer select-none"
                                 title="Klik aku untuk sapaan hangat!">
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= FASILITAS & KEUNGGULAN ================= --}}
    <section id="fasilitas" class="py-20 lg:py-28 bg-white border-b border-slate-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Fasilitas dengan Animasi Soft Reveal --}}
            <div class="text-center max-w-2xl mx-auto mb-14 reveal-soft">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Mengapa Memilih Kami?
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">Pelayanan medis berkualitas dengan kenyamanan dan keselamatan pasien sebagai prioritas utama.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Fasilitas 1 --}}
                <div class="reveal-soft" style="transition-delay: 100ms;">
                    <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#2d4499] flex items-center justify-center text-xl mb-4">
                            <i class="fas fa-display"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Bebas Antre & Nyaman</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pantau nomor antrean langsung dari HP Anda. Tidak perlu lagi berdesakan atau menunggu lama di ruang tunggu.
                        </p>
                    </div>
                </div>

                {{-- Fasilitas 2 --}}
                <div class="reveal-soft" style="transition-delay: 200ms;">
                    <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mb-4">
                            <i class="fas fa-user-doctor"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Dokter Spesialis Berpengalaman</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pemeriksaan menyeluruh oleh dokter ahli berpengalaman dan penuh kehati-hatian.
                        </p>
                    </div>
                </div>

                {{-- Fasilitas 3 --}}
                <div class="reveal-soft" style="transition-delay: 300ms;">
                    <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl mb-4">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Resep Tepat & Aman</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Layanan farmasi terpadu yang memastikan ketersediaan obat asli dengan penjelasan aturan pakai yang jelas.
                        </p>
                    </div>
                </div>

                {{-- Fasilitas 4 --}}
                <div class="reveal-soft" style="transition-delay: 400ms;">
                    <div class="h-full p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl mb-4">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Biaya Transparan</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Sistem tagihan yang transparan membuat Anda bisa fokus sepenuhnya pada kesembuhan.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= SECTION LAYANAN POLI SPESIALIS ================= --}}
    <section id="layanan" class="py-20 bg-slate-50 border-b border-slate-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16 reveal-soft">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Layanan Medis Spesialis
                </h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Berbagai pilihan poliklinik dengan peralatan medis modern, siap memberikan penanganan yang akurat dan tepat untuk Anda sekeluarga.
                </p>
            </div>

            {{-- Grid Poli --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($polis as $poli)
                <div class="reveal-soft" style="transition-delay: {{ ($loop->index % 3) * 120 }}ms;">
                    <div class="h-full card bg-white shadow-md hover:shadow-xl rounded-2xl border border-slate-200/90 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between">
                        <div class="p-7 flex flex-col h-full">
                            <div class="flex items-center justify-between mb-5">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#2d4499] flex items-center justify-center text-xl font-bold border border-blue-100">
                                    <i class="fas fa-stethoscope"></i>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    {{ $poli->dokters_count ?? 0 }} Dokter Praktik
                                </span>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 mb-2">
                                {{ $poli->nama_poli }}
                            </h3>
                            <p class="text-slate-500 text-xs leading-relaxed line-clamp-3 mb-6 flex-grow">
                                {{ $poli->keterangan ?: 'Layanan pemeriksaan kesehatan spesialis dengan dokter berpengalaman dan penanganan medis komprehensif.' }}
                            </p>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-[10px]"></i> Sesuai Jadwal Praktik
                                </span>
                                @auth
                                    <a href="{{ route('pasien.daftar') }}" class="text-xs font-bold text-[#2d4499] hover:underline flex items-center gap-1">
                                        Daftar Periksa <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="text-xs font-bold text-[#2d4499] hover:underline flex items-center gap-1">
                                        Pilih Jadwal <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12 text-slate-400 reveal-soft">
                    Daftar poliklinik sedang diperbarui.
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= ALUR PASIEN ================= --}}
    <section id="alur" class="py-20 bg-white border-b border-slate-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16 reveal-soft">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                   Kemudahan Berobat dalam 4 Langkah
                </h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Kami memastikan setiap tahap kunjungan Anda berjalan lancar, jelas, dan tanpa ribet.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                
                {{-- Step 1 --}}
                <div class="reveal-soft" style="transition-delay: 100ms;">
                    <div class="h-full text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                            1
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Lengkapi Data Diri</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Cukup sekali daftar untuk mengamankan rekam medis dan riwayat kesehatan Anda.
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="reveal-soft" style="transition-delay: 200ms;">
                    <div class="h-full text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                            2
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Atur Waktu Kunjungan</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pilih jadwal dokter favorit Anda dan dapatkan kepastian nomor urut pemeriksaan.
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="reveal-soft" style="transition-delay: 300ms;">
                    <div class="h-full text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                            3
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Konsultasi Tatap Muka</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Konsultasi nyaman dan pemeriksaan menyeluruh langsung bersama dokter ahli.
                        </p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="reveal-soft" style="transition-delay: 400ms;">
                    <div class="h-full text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                            4
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Selesai Tanpa Kendala</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Ambil obat yang diresepkan dan lakukan pembayaran dengan berbagai opsi transaksi yang fleksibel.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- ================= JADWAL DOKTER SPESIALIS ================= --}}
    <section id="jadwal" class="py-20 bg-slate-50 border-b border-slate-200 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-wrap items-center justify-center gap-4 mb-14 text-center reveal-soft">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Jadwal Praktik Dokter
                    </h2>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">Cek kepastian hari dan jam praktik dokter spesialis pilihan Anda di bawah ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($jadwals as $jadwal)
                <div class="reveal-soft" style="transition-delay: {{ ($loop->index % 3) * 120 }}ms;">
                    <div class="h-full bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-[#2d4499] flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-user-doctor"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800 text-sm">{{ optional($jadwal->dokter)->nama ?? 'Dokter' }}</h3>
                                        <p class="text-xs text-slate-400">{{ optional($jadwal->dokter->poli)->nama_poli ?? 'Poliklinik' }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Praktik
                                </span>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-700 mt-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-day text-[#2d4499]"></i>
                                <span>{{ $jadwal->hari }}</span>
                            </div>
                            <div class="flex items-center gap-2 font-mono text-slate-600">
                                <i class="fas fa-clock text-slate-400"></i>
                                <span>{{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }} WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-10 text-slate-400 reveal-soft">
                    Jadwal praktik dokter sedang disinkronkan.
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= FOOTER RUMAH SAKIT LENGKAP ================= --}}
    <footer id="kontak" class="bg-slate-900 text-slate-400 text-xs py-16 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-800 reveal-soft">
                
                {{-- Col 1: Identitas Klinik --}}
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="font-extrabold text-xl text-white tracking-tight">Poli<span class="text-blue-400">Klinik</span></span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-md">
                        Berdedikasi memberikan pelayanan rawat jalan profesional yang mengutamakan mutu, standar, dan pemanfaatan sistem manajemen medis modern.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center hover:bg-[#2d4499] transition">
                            <i class="fab fa-facebook-f text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center hover:bg-[#2d4499] transition">
                            <i class="fab fa-instagram text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center hover:bg-[#2d4499] transition">
                            <i class="fab fa-whatsapp text-xs"></i>
                        </a>
                    </div>
                </div>

                {{-- Col 2: Jam Pelayanan --}}
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm">Jam Pelayanan Poliklinik</h4>
                    <p class="leading-relaxed">
                        <strong class="text-slate-300 block">Senin - Jumat:</strong> 08:00 - 20:00 WIB
                    </p>
                    <p class="leading-relaxed">
                        <strong class="text-slate-300 block">Sabtu:</strong> 08:00 - 15:00 WIB
                    </p>
                    <p class="text-amber-400 font-semibold">
                        Minggu & Hari Libur Nasional: Tutup
                    </p>
                </div>

                {{-- Col 3: Informasi Kontak --}}
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm">Pusat Informasi & Lokasi</h4>
                    <p class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-[#2d4499] mt-0.5"></i>
                        <span>Jl. Kesehatan Raya No. 45, Kota Semarang, Jawa Tengah</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fas fa-phone text-[#2d4499]"></i>
                        <span>(024) 7654-3210</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fas fa-envelope text-[#2d4499]"></i>
                        <span>layanan@poliklinik-sehat.id</span>
                    </p>
                </div>

            </div>

            {{-- Copyright & Legal --}}
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} PoliKlinik. Seluruh Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-slate-300 transition">Kebijakan Privasi Pasien</a>
                    <a href="#" class="hover:text-slate-300 transition">Tata Tertib Pelayanan</a>
                    <a href="{{ route('login') }}" class="hover:text-slate-300 transition">Akses Staf Medis</a>
                </div>
            </div>

        </div>
    </footer>

    {{-- Mascot Interactive Interaction Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mascot = document.getElementById('heroMascot');
            if (!mascot) return;

            const greetings = [
                "Yuk, jaga kesehatanmu hari ini! 🌟",
                "Dokter kami selalu siap melayani dengan ramah! 🩺",
                "Pendaftaran antrian online bikin waktu lebih hemat! ⏱️",
                "Jangan lupa minum cukup air putih ya! 💧",
                "Semoga lekas pulih dan sehat selalu! 💚"
            ];

            let greetIndex = 0;
            const bubbleText = document.querySelector('#beranda .anim-float-fast span:last-child');

            mascot.addEventListener('click', function () {
                greetIndex = (greetIndex + 1) % greetings.length;
                if (bubbleText) {
                    bubbleText.textContent = greetings[greetIndex];
                }
                mascot.style.transition = 'transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                mascot.style.transform = 'scale(1.12) rotate(' + (Math.random() > 0.5 ? '4deg' : '-4deg') + ')';
                setTimeout(() => {
                    mascot.style.transform = '';
                }, 300);
            });

            // Scroll Reveal Animation (Soft Entrance on Scroll)
            const revealElements = document.querySelectorAll('.reveal-soft');
            if (revealElements.length > 0 && 'IntersectionObserver' in window) {
                const revealObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            revealObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.15,
                    rootMargin: '0px 0px -40px 0px'
                });

                revealElements.forEach((el) => revealObserver.observe(el));
            } else {
                // Fallback jika browser tidak mendukung observer
                revealElements.forEach((el) => el.classList.add('is-revealed'));
            }

            // Mobile Menu Toggle & Smooth Auto-Close
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuIcon = document.getElementById('mobileMenuIcon');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function () {
                    const isClosed = mobileMenu.classList.contains('hidden');
                    if (isClosed) {
                        mobileMenu.classList.remove('hidden');
                        if (mobileMenuIcon) {
                            mobileMenuIcon.classList.remove('fa-bars');
                            mobileMenuIcon.classList.add('fa-xmark');
                        }
                    } else {
                        mobileMenu.classList.add('hidden');
                        if (mobileMenuIcon) {
                            mobileMenuIcon.classList.remove('fa-xmark');
                            mobileMenuIcon.classList.add('fa-bars');
                        }
                    }
                });

                // Auto-close menu when a link is clicked
                mobileNavLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        mobileMenu.classList.add('hidden');
                        if (mobileMenuIcon) {
                            mobileMenuIcon.classList.remove('fa-xmark');
                            mobileMenuIcon.classList.add('fa-bars');
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>

