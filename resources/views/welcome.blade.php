<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PoliSehat</title>

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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-[#2d4499] selection:text-white">

    {{-- ================= NAVBAR ================= --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="/" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-2xl bg-[#2d4499] text-white flex items-center justify-center shadow-md shadow-indigo-950/20 group-hover:scale-105 transition-transform duration-200">
                    <i class="fas fa-hospital-user text-xl"></i>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 block leading-tight">
                        Poli<span class="text-[#2d4499]">Sehat</span>
                    </span>
                    <span class="text-[11px] font-semibold text-slate-400 tracking-wider uppercase block">Klinik Terakreditasi</span>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#beranda" class="hover:text-[#2d4499] transition">Beranda</a>
                <a href="#layanan" class="hover:text-[#2d4499] transition">Layanan Spesialis</a>
                <a href="#fasilitas" class="hover:text-[#2d4499] transition">Fasilitas</a>
                <a href="#alur" class="hover:text-[#2d4499] transition">Alur Pasien</a>
                <a href="#jadwal" class="hover:text-[#2d4499] transition">Jadwal Praktik</a>
                <a href="#pembayaran" class="hover:text-[#2d4499] transition">Metode Pembayaran</a>
            </nav>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    @php
                        $role = auth()->user()->role;
                        $dashboardRoute = route('login');
                        if ($role === 'admin') $dashboardRoute = route('admin.dashboard');
                        elseif ($role === 'dokter') $dashboardRoute = route('dokter.dashboard');
                        elseif ($role === 'pasien') $dashboardRoute = route('pasien.dashboard');
                    @endphp
                    <a href="{{ $dashboardRoute }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white text-sm font-bold shadow-md shadow-indigo-900/10 transition-all hover:scale-102">
                        <i class="fas fa-user-check text-xs"></i>
                        <span>Dashboard Pasien</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-slate-700 hover:text-[#2d4499] hover:bg-slate-100 text-sm font-bold transition">
                        <i class="fas fa-right-to-bracket text-xs"></i>
                        <span>Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white text-sm font-bold shadow-md shadow-indigo-900/10 transition-all hover:scale-102">
                        <i class="fas fa-calendar-plus text-xs"></i>
                        <span>Daftar Berobat</span>
                    </a>
                @endauth
            </div>

        </div>
    </header>

    {{-- ================= HERO SECTION ================= --}}
    <section id="beranda" class="hero-pattern pt-12 pb-20 lg:pt-20 lg:pb-32 overflow-hidden border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                {{-- Left Text --}}
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">

                    {{-- Main Headline --}}
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.18]">
                        Kesehatan Keluarga Anda, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#2d4499] via-indigo-600 to-blue-600">Prioritas Utama Kami.</span>
                    </h1>

                    {{-- Subheadline Medis --}}
                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Klinik rawat jalan terpadu dengan dokter spesialis berpengalaman. Nikmati kemudahan pendaftaran antrian secara daring, pemantauan giliran periksa secara transparan, dan rekam medis digital yang aman.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                        @auth
                            <a href="{{ route('pasien.daftar') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-2xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-bold text-base shadow-lg shadow-indigo-900/20 transition-all hover:scale-102">
                                <i class="fas fa-calendar-check"></i>
                                <span>Pendaftaran Pemeriksaan</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-2xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-bold text-base shadow-lg shadow-indigo-900/20 transition-all hover:scale-102">
                                <i class="fas fa-user-plus"></i>
                                <span>Daftar Pasien Baru</span>
                            </a>
                        @endauth
                        <a href="#jadwal"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-base border border-slate-200 shadow-sm transition">
                            <i class="fas fa-user-doctor text-[#2d4499]"></i>
                            <span>Jadwal Dokter Spesialis</span>
                        </a>
                    </div>

                    {{-- Data Kepercayaan Pasien --}}
                    <div class="pt-6 border-t border-slate-200/60 grid grid-cols-3 gap-6 text-center lg:text-left">
                        <div>
                            <span class="block text-2xl font-black text-slate-900">{{ $poliCount ?? 4 }}+</span>
                            <span class="text-xs text-slate-500 font-medium">Poliklinik Spesialis</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-slate-900">{{ $dokterCount ?? 5 }}+</span>
                            <span class="text-xs text-slate-500 font-medium">Dokter Spesialis Ahli</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-[#2d4499]">4.9 / 5.0</span>
                            <span class="text-xs text-slate-500 font-medium">Kepuasan Pasien</span>
                        </div>
                    </div>

                </div>

                {{-- Right Visual: Animasi Kartun Bergerak & Floating Badges --}}
                <div class="lg:col-span-5 relative py-6">
                    <div class="relative mx-auto max-w-[440px] flex items-center justify-center min-h-[460px]">
                        
                        {{-- Ambient Animated Glows --}}
                        <div class="absolute w-80 h-80 bg-gradient-to-tr from-blue-300/40 via-indigo-300/30 to-purple-300/40 rounded-full blur-3xl anim-pulse-glow -z-10 pointer-events-none"></div>
                        <div class="absolute -bottom-6 -right-6 w-56 h-56 bg-emerald-200/40 rounded-full blur-2xl anim-float-slow -z-10 pointer-events-none" style="animation-duration: 7s;"></div>

                        {{-- Floating Micro Elements (Sparkles, Heart, Pill) --}}
                        <div class="absolute top-2 left-6 text-xl anim-twinkle select-none pointer-events-none z-20">✨</div>
                        <div class="absolute top-1/4 -right-2 text-2xl anim-heartbeat select-none pointer-events-none z-20">❤️</div>
                        <div class="absolute bottom-16 -left-3 text-2xl anim-float-fast select-none pointer-events-none z-20">💊</div>
                        <div class="absolute bottom-6 right-8 text-xl anim-twinkle select-none pointer-events-none z-20" style="animation-delay: -1.2s;">✨</div>

                        {{-- Floating Speech Bubble --}}
                        <div class="absolute -top-6 right-2 sm:right-6 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-2 anim-float-fast z-30">
                            <span class="text-sm">👋</span>
                            <span class="text-xs font-bold text-slate-700">Yuk, jaga kesehatanmu!</span>
                            <div class="absolute -bottom-1.5 right-6 w-3 h-3 bg-white rotate-45 border-r border-b border-slate-100"></div>
                        </div>

                        {{-- Main Cartoon Character (Animated) --}}
                        <div class="relative z-10 w-full flex items-center justify-center p-2">
                            <img src="{{ asset('images/hero-mascot-original.png') }}"
                                 alt="Karakter Medis Kartun PoliSehat"
                                 id="heroMascot"
                                 class="w-full max-w-[340px] sm:max-w-[400px] object-contain drop-shadow-2xl anim-float-slow transition-transform duration-300 hover:scale-105 active:scale-95 cursor-pointer select-none"
                                 title="Klik aku untuk sapaan hangat!">
                        </div>

                        {{-- Floating Card 1 (Top Left): Dokter Siaga --}}
                        <div class="absolute -top-3 -left-3 sm:-left-8 bg-white/95 backdrop-blur-md px-3.5 py-2.5 rounded-2xl shadow-xl border border-slate-100/80 flex items-center gap-3 anim-float-reverse z-20 hover:scale-105 transition-transform">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold shadow-sm">
                                🩺
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    <span class="text-xs font-bold text-slate-800">Dokter Siaga</span>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium">Pelayanan Rawat Jalan</span>
                            </div>
                        </div>

                        {{-- Floating Card 2 (Top Right): Antrian Aktif Terpantau --}}
                        <div class="absolute top-12 -right-2 sm:-right-8 bg-white/95 backdrop-blur-md px-3.5 py-2.5 rounded-2xl shadow-xl border border-slate-100/80 flex items-center gap-3 anim-float-slow z-20 hover:scale-105 transition-transform" style="animation-delay: -1.8s;">
                            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-[#2d4499] flex items-center justify-center text-lg shadow-sm">
                                ⚡
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Antrian Terkini</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-black text-slate-900 font-mono">A - 012</span>
                                    <span class="text-[9px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.2 rounded-full">Poli Umum</span>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Card 3 (Bottom Left): Resep Apotek Digital --}}
                        <div class="absolute -bottom-4 -left-3 sm:-left-8 bg-white/95 backdrop-blur-md px-3.5 py-2.5 rounded-2xl shadow-xl border border-slate-100/80 flex items-center gap-3 anim-float-slow z-20 hover:scale-105 transition-transform" style="animation-delay: -3s;">
                            <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-lg shadow-sm">
                                💊
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Resep Obat Resmi</span>
                                <span class="text-[10px] text-slate-500 font-medium">Pengambilan Cepat & Akurat</span>
                            </div>
                        </div>

                        {{-- Floating Card 4 (Bottom Right): Pembayaran QRIS & VA --}}
                        <div class="absolute -bottom-2 -right-2 sm:-right-6 bg-white/95 backdrop-blur-md px-3.5 py-2.5 rounded-2xl shadow-xl border border-slate-100/80 flex items-center gap-3 anim-float-reverse z-20 hover:scale-105 transition-transform" style="animation-delay: -4s;">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base shadow-sm">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Pembayaran Instan</span>
                                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded-full inline-block">QRIS & VA Midtrans</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= FASILITAS & KEUNGGULAN ================= --}}
    <section id="fasilitas" class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-12">
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                    Fasilitas & Keunggulan Pelayanan
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Kami berkomitmen memberikan pelayanan medis bermutu tinggi dan berorientasi pada keselamatan pasien.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Fasilitas 1 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#2d4499] flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-display"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Sistem Antrian Terintegrasi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pantau giliran pemeriksaan dari perangkat Anda tanpa harus berkerumun lama di ruang tunggu klinik.
                    </p>
                </div>

                {{-- Fasilitas 2 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-user-doctor"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Dokter Spesialis Berpengalaman</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pemeriksaan dilakukan secara komprehensif oleh dokter ahli bersertifikasi dengan empati dan kehati-hatian.
                    </p>
                </div>

                {{-- Fasilitas 3 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Instalasi Farmasi Lengkap</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Penyediaan obat-obatan asli berkualitas tinggi sesuai dengan resep dan petunjuk penggunaan dokter.
                    </p>
                </div>

                {{-- Fasilitas 4 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Rincian Biaya Transparan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Rincian jasa medis dan harga resep obat tercatat dengan jelas tanpa ada biaya tersembunyi.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= SECTION LAYANAN POLI SPESIALIS ================= --}}
    <section id="layanan" class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                    Layanan Medis Spesialis
                </h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Tersedia berbagai poliklinik dengan sarana diagnostik modern untuk memenuhi kebutuhan kesehatan Anda dan keluarga.
                </p>
            </div>

            {{-- Grid Poli --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($polis as $poli)
                <div class="card bg-white shadow-md hover:shadow-xl rounded-2xl border border-slate-200/90 transition-all duration-300 hover:-translate-y-1">
                    <div class="p-7">
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
                        <p class="text-slate-500 text-xs leading-relaxed line-clamp-3 mb-6">
                            {{ $poli->keterangan ?: 'Layanan pemeriksaan kesehatan spesialis dengan dokter berpengalaman dan penanganan medis komprehensif.' }}
                        </p>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
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
                @empty
                <div class="col-span-3 text-center py-12 text-slate-400">
                    Daftar poliklinik sedang diperbarui.
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= ALUR PASIEN ================= --}}
    <section id="alur" class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                    Tahapan Praktis Pelayanan Pasien
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Dirancang untuk kenyamanan, efisiensi waktu, dan kepuasan pasien sejak pendaftaran hingga pemulihan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                
                {{-- Step 1 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        1
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Registrasi Pasien</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lengkapi data diri pasien untuk pencatatan rekam medis yang valid dan terorganisir.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        2
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Pilih Dokter & Jadwal</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pilih poli tujuan dan dokter pemeriksa, lalu dapatkan nomor antrian resmi poliklinik.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        3
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Pemeriksaan Dokter</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Konsultasi dan pemeriksaan klinis oleh dokter spesialis, serta penetapan anjuran medis.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        4
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Penebusan Obat & Kasir</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Ambil obat di farmasi klinik dan selesaikan pembayaran secara non-tunai atau tunai dengan mudah.
                    </p>
                </div>

            </div>

        </div>
    </section>

    {{-- ================= JADWAL DOKTER SPESIALIS ================= --}}
    <section id="jadwal" class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-wrap items-center justify-center gap-4 mb-12 text-center">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">
                        Jadwal Kehadiran Dokter Spesialis
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Jadwal praktik dokter spesialis rawat jalan tertera di bawah ini untuk kemudahan konsultasi Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($jadwals as $jadwal)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition">
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

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-700">
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
                @empty
                <div class="col-span-3 text-center py-10 text-slate-400">
                    Jadwal praktik dokter sedang disinkronkan.
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= FOOTER RUMAH SAKIT LENGKAP ================= --}}
    <footer id="kontak" class="bg-slate-900 text-slate-400 text-xs py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-800">
                
                {{-- Col 1: Identitas Klinik --}}
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#2d4499] text-white flex items-center justify-center font-bold">
                            <i class="fas fa-hospital-user text-base"></i>
                        </div>
                        <span class="font-extrabold text-xl text-white tracking-tight">Poli<span class="text-blue-400">Sehat</span></span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-md">
                        Klinik Pratama Poliklinik Sehat berdedikasi memberikan pelayanan rawat jalan profesional yang mengutamakan mutu, keramahan, dan pemanfaatan sistem manajemen medis modern.
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
                <p>&copy; {{ date('Y') }} Klinik Pratama Poliklinik Sehat. Seluruh Hak Cipta Dilindungi.</p>
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
        });
    </script>
</body>
</html>

