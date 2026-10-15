<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poliklinik Sehat - Sistem Layanan Kesehatan & Antrian Online Terpadu</title>

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
            background-image: radial-gradient(#2d449918 1px, transparent 1px), radial-gradient(#2d449918 1px, #f8fafc 1px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-[#2d4499] selection:text-white">

    {{-- ================= NAVBAR ================= --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-[#2d4499] text-white flex items-center justify-center shadow-md shadow-indigo-950/20 group-hover:scale-105 transition-transform duration-200">
                    <i class="fas fa-heart-pulse text-xl"></i>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 block leading-tight">
                        Poli<span class="text-[#2d4499]">Sehat</span>
                    </span>
                    <span class="text-[11px] font-semibold text-slate-400 tracking-wider uppercase block">Poliklinik Terpadu</span>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#beranda" class="hover:text-[#2d4499] transition">Beranda</a>
                <a href="#layanan" class="hover:text-[#2d4499] transition">Layanan Poli</a>
                <a href="#alur" class="hover:text-[#2d4499] transition">Alur Berobat</a>
                <a href="#jadwal" class="hover:text-[#2d4499] transition">Jadwal Dokter</a>
                <a href="#pembayaran" class="hover:text-[#2d4499] transition">Bayar Online</a>
                <a href="#faq" class="hover:text-[#2d4499] transition">FAQ</a>
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
                        <i class="fas fa-columns text-xs"></i>
                        <span>Ke Dashboard ({{ ucfirst($role) }})</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-slate-700 hover:text-[#2d4499] hover:bg-slate-100/80 text-sm font-bold transition">
                        <i class="fas fa-right-to-bracket text-xs"></i>
                        <span>Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white text-sm font-bold shadow-md shadow-indigo-900/10 transition-all hover:scale-102">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span>Daftar Pasien</span>
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
                    
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-[#2d4499] text-xs font-bold shadow-xs">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <i class="fas fa-bolt text-amber-500"></i>
                        <span>Antrian Real-Time & Pembayaran Instan via Midtrans</span>
                    </div>

                    {{-- Main Headline --}}
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Pelayanan Medis Modern, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#2d4499] via-indigo-600 to-sky-600">Cepat & Tanpa Antri Lama.</span>
                    </h1>

                    {{-- Subheadline --}}
                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Daftar jadwal periksa spesialis langsung dari rumah. Pantau nomor antrian secara langsung (*live update*) tanpa reload halaman, dan nikmati kemudahan pembayaran digital otomatis.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                        @auth
                            <a href="{{ route('pasien.daftar') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-2xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-bold text-base shadow-lg shadow-indigo-900/20 transition-all hover:scale-102">
                                <i class="fas fa-calendar-check"></i>
                                <span>Daftar Periksa Sekarang</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-2xl bg-[#2d4499] hover:bg-[#1e2d6b] text-white font-bold text-base shadow-lg shadow-indigo-900/20 transition-all hover:scale-102">
                                <i class="fas fa-clipboard-check"></i>
                                <span>Daftar Pasien Baru</span>
                            </a>
                        @endauth
                        <a href="#layanan"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-base border border-slate-200 shadow-sm transition">
                            <i class="fas fa-stethoscope text-[#2d4499]"></i>
                            <span>Lihat Layanan Poli</span>
                        </a>
                    </div>

                    {{-- Micro Trust Indicators --}}
                    <div class="pt-6 border-t border-slate-200/60 grid grid-cols-3 gap-4 text-center lg:text-left">
                        <div>
                            <span class="block text-2xl font-black text-slate-900">{{ $poliCount ?? 4 }}+</span>
                            <span class="text-xs text-slate-500 font-medium">Poli Spesialis</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-slate-900">{{ $dokterCount ?? 5 }}+</span>
                            <span class="text-xs text-slate-500 font-medium">Dokter Ahli</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-black text-[#2d4499]">100%</span>
                            <span class="text-xs text-slate-500 font-medium">Digital & Cashless</span>
                        </div>
                    </div>

                </div>

                {{-- Right Visual: Mockup Card Antrian Live --}}
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md">
                        
                        {{-- Background Glow --}}
                        <div class="absolute -top-10 -left-10 w-72 h-72 bg-blue-300/40 rounded-full blur-3xl -z-10 pointer-events-none"></div>
                        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-indigo-300/40 rounded-full blur-3xl -z-10 pointer-events-none"></div>

                        {{-- Card Utama --}}
                        <div class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-200/90 relative overflow-hidden">
                            
                            {{-- Card Header --}}
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="font-bold text-slate-800 text-sm">Status Antrian Poliklinik</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-[#2d4499] border border-blue-200">
                                    Live WebSocket
                                </span>
                            </div>

                            {{-- Nomor Antrian Dipanggil Showcase --}}
                            <div class="my-5 p-5 bg-gradient-to-br from-[#2d4499] to-indigo-700 rounded-2xl text-white text-center shadow-md relative overflow-hidden">
                                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full"></div>
                                <span class="text-xs uppercase tracking-widest text-indigo-200 font-bold block mb-1">
                                    Sedang Dilayani di Poli Umum
                                </span>
                                <div class="text-5xl font-black font-mono tracking-tight my-1">
                                    A - 012
                                </div>
                                <span class="text-xs text-indigo-100 flex items-center justify-center gap-1.5 mt-2">
                                    <i class="fas fa-user-doctor text-xs"></i>
                                    dr. Spesialis Sedang Memeriksa
                                </span>
                            </div>

                            {{-- Mini Feature List --}}
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                            <i class="fas fa-qrcode"></i>
                                        </div>
                                        <span class="font-semibold text-slate-700">Pembayaran QRIS & VA Midtrans</span>
                                    </div>
                                    <span class="text-emerald-600 font-bold">Otomatis Lunas</span>
                                </div>

                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-[#2d4499] flex items-center justify-center text-xs font-bold">
                                            <i class="fas fa-file-medical"></i>
                                        </div>
                                        <span class="font-semibold text-slate-700">Rekam Medis & Resep Obat</span>
                                    </div>
                                    <span class="text-blue-600 font-bold">Tersimpan Aman</span>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= FITUR UNGGULAN STRIP ================= --}}
    <section class="py-12 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Fitur 1 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#2d4499] flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-tower-broadcast"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Antrian Real-Time</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Nomor antrian terupdate otomatis berkat teknologi Laravel Reverb tanpa perlu me-refresh halaman berulang kali.
                    </p>
                </div>

                {{-- Fitur 2 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Bayar Online Midtrans</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Selesaikan tagihan periksa via QRIS, Virtual Account (BCA, Mandiri, BRI) langsung terverifikasi seketika.
                    </p>
                </div>

                {{-- Fitur 3 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-user-doctor"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Dokter Terverifikasi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Ditangani langsung oleh para dokter spesialis profesional yang berdedikasi tinggi memberikan diagnosa akurat.
                    </p>
                </div>

                {{-- Fitur 4 --}}
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1.5">Rekam Medis Transparan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Riwayat diagnosa dokter, resep obat, catatan medis, dan rincian biaya dapat Anda akses kapan saja.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= SECTION LAYANAN POLI ================= --}}
    <section id="layanan" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Section Title --}}
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#2d4499] uppercase tracking-wider">
                    Layanan Poliklinik
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                    Pilihan Poliklinik Spesialis
                </h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Kami menyediakan berbagai poliklinik dengan sarana medis modern dan dokter spesialis yang siap melayani kebutuhan Anda.
                </p>
            </div>

            {{-- Grid Poli --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($polis as $poli)
                <div class="card bg-white shadow-md hover:shadow-xl rounded-2xl border border-slate-200/90 transition-all duration-300 hover:-translate-y-1">
                    <div class="p-7">
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#2d4499] flex items-center justify-center text-xl font-bold border border-blue-100">
                                <i class="fas fa-hospital-user"></i>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                {{ $poli->dokters_count ?? 0 }} Dokter Spesialis
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
                                <i class="fas fa-circle-check text-[10px]"></i> Buka Setiap Hari Praktik
                            </span>
                            @auth
                                <a href="{{ route('pasien.daftar') }}" class="text-xs font-bold text-[#2d4499] hover:underline flex items-center gap-1">
                                    Daftar <i class="fas fa-arrow-right text-[10px]"></i>
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
                    Belum ada data poliklinik yang ditampilkan.
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= ALUR BEROBAT ================= --}}
    <section id="alur" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-[#2d4499] uppercase tracking-wider">
                    Alur Mudah
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                    4 Langkah Praktis Berobat di Poliklinik
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Proses terintegrasi mulai dari pendaftaran antrian hingga pemeriksaan dan pembayaran selesai.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                
                {{-- Step 1 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        1
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Daftar Akun Pasien</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Registrasi data diri Anda untuk mendapatkan Nomor Rekam Medis (RM) resmi dari klinik.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        2
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Pilih Poli & Dokter</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Tentukan poli tujuan, dokter spesialis, jadwal praktik, serta tuliskan keluhan awal Anda.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-[#2d4499] text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        3
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Pantau Live Antrian</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Nomor antrian Anda muncul di dashboard dan diperbarui secara real-time tanpa perlu duduk lama di ruang tunggu.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="text-center p-6 rounded-2xl bg-slate-50 border border-slate-100 relative">
                    <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-lg mx-auto mb-4 shadow-md">
                        4
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">Periksa & Bayar Online</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lakukan pemeriksaan medis, dapatkan resep obat, lalu bayar secara praktis melalui Midtrans (QRIS / VA).
                    </p>
                </div>

            </div>

        </div>
    </section>

    {{-- ================= JADWAL DOKTER SECTION ================= --}}
    <section id="jadwal" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-wrap items-end justify-between gap-4 mb-12">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#2d4499] uppercase tracking-wider">
                        Jadwal Dokter
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">
                        Jadwal Praktik Dokter Spesialis
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Cek jadwal kehadiran dokter kami untuk memudahkan rencana kunjungan Anda.</p>
                </div>
                
                @auth
                <a href="{{ route('pasien.daftar') }}" class="px-5 py-2.5 bg-[#2d4499] text-white rounded-xl text-xs font-bold hover:bg-[#1e2d6b] transition">
                    Pilih Jadwal & Periksa
                </a>
                @else
                <a href="{{ route('login') }}" class="px-5 py-2.5 bg-[#2d4499] text-white rounded-xl text-xs font-bold hover:bg-[#1e2d6b] transition">
                    Login untuk Mendaftar
                </a>
                @endauth
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
                            Aktif
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
                    Jadwal praktik dokter sedang diperbarui.
                </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= SHOWCASE PEMBAYARAN ONLINE (MIDTRANS) ================= --}}
    <section id="pembayaran" class="py-20 bg-gradient-to-br from-slate-900 to-indigo-950 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                {{-- Left Text --}}
                <div class="space-y-6">
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">
                        <i class="fas fa-wallet mr-1 text-amber-300"></i> Pembayaran Cashless Otomatis
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                        Bayar Tagihan Berobat Jadi Lebih Cepat dengan Midtrans
                    </h2>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Tidak perlu mengantri lagi di kasir. Selesaikan pembayaran biaya konsultasi dokter dan obat resep langsung dari smartphone Anda menggunakan berbagai metode pembayaran favorit:
                    </p>

                    <div class="grid grid-cols-2 gap-4 text-xs pt-2">
                        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white/5 border border-white/10">
                            <i class="fas fa-qrcode text-xl text-emerald-400"></i>
                            <div>
                                <span class="font-bold block text-white">QRIS Dinamis</span>
                                <span class="text-slate-400 text-[11px]">GoPay, OVO, Dana, Shopee</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-white/5 border border-white/10">
                            <i class="fas fa-building-columns text-xl text-blue-400"></i>
                            <div>
                                <span class="font-bold block text-white">Virtual Account</span>
                                <span class="text-slate-400 text-[11px]">BCA, Mandiri, BRI, BNI</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-2 text-xs text-slate-300">
                        <i class="fas fa-circle-check text-emerald-400"></i>
                        <span>Status pembayaran langsung berubah lunas secara instan setelah pembayaran terkonfirmasi.</span>
                    </div>
                </div>

                {{-- Right Card Demo --}}
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-7 border border-white/15 max-w-md mx-auto shadow-2xl">
                    <div class="text-xs text-slate-300 pb-3 border-b border-white/10 flex justify-between items-center">
                        <span>Simulasi Pembayaran Pasien</span>
                        <span class="text-emerald-400 font-bold">Terverifikasi Otomatis</span>
                    </div>

                    <div class="py-5 space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Konsultasi Dokter:</span>
                            <span class="font-mono text-white font-semibold">Rp 150.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Resep Obat Medis:</span>
                            <span class="font-mono text-white font-semibold">Rp 65.000</span>
                        </div>
                        <div class="border-t border-dashed border-white/20 pt-3 flex justify-between items-center">
                            <span class="font-bold text-white">Total Tagihan:</span>
                            <span class="text-xl font-mono font-black text-amber-400">Rp 215.000</span>
                        </div>
                    </div>

                    <div class="mt-4 p-3.5 bg-white/5 rounded-2xl border border-white/10 text-center">
                        <span class="text-xs text-slate-300 block mb-2">Metode Didukung:</span>
                        <div class="flex items-center justify-center gap-4 text-2xl text-slate-300">
                            <i class="fab fa-cc-visa" title="Visa"></i>
                            <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                            <i class="fas fa-qrcode text-emerald-400" title="QRIS"></i>
                            <i class="fas fa-money-check text-blue-400" title="Virtual Account"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= FAQ SECTION ================= --}}
    <section id="faq" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 uppercase tracking-wider">
                    Bantuan
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
            </div>

            <div class="space-y-4">
                <div class="collapse collapse-plus bg-slate-50 border border-slate-200 rounded-2xl">
                    <input type="radio" name="faq-accordion" checked="checked" />
                    <div class="collapse-title text-sm font-bold text-slate-800">
                        Apakah saya bisa mendaftar antrian poliklinik dari rumah?
                    </div>
                    <div class="collapse-content text-xs text-slate-600 leading-relaxed">
                        <p>Ya, tentu saja. Pasien dapat membuat akun, login, dan mendaftar jadwal periksa dokter dari rumah. Anda akan langsung mendapatkan nomor antrian resmi yang tersimpan di akun Anda.</p>
                    </div>
                </div>

                <div class="collapse collapse-plus bg-slate-50 border border-slate-200 rounded-2xl">
                    <input type="radio" name="faq-accordion" />
                    <div class="collapse-title text-sm font-bold text-slate-800">
                        Bagaimana cara kerja antrian real-time?
                    </div>
                    <div class="collapse-content text-xs text-slate-600 leading-relaxed">
                        <p>Sistem kami menggunakan teknologi WebSocket (Laravel Reverb). Ketika dokter memanggil pasien berikutnya di ruang poli, tampilan nomor antrian di layar pasien akan langsung terupdate otomatis tanpa perlu memuat ulang halaman browser.</p>
                    </div>
                </div>

                <div class="collapse collapse-plus bg-slate-50 border border-slate-200 rounded-2xl">
                    <input type="radio" name="faq-accordion" />
                    <div class="collapse-title text-sm font-bold text-slate-800">
                        Apakah tersedia opsi pembayaran transfer manual?
                    </div>
                    <div class="collapse-content text-xs text-slate-600 leading-relaxed">
                        <p>Ya! Selain pembayaran otomatis instan via Midtrans (QRIS dan Virtual Account), pasien juga tetap dapat mengunggah foto struk transfer manual yang nantinya akan dikonfirmasi oleh Admin poliklinik.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer id="kontak" class="bg-slate-900 text-slate-400 text-xs py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-800">
                
                {{-- Col 1: Brand & Desc --}}
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#2d4499] text-white flex items-center justify-center font-bold">
                            <i class="fas fa-heart-pulse text-base"></i>
                        </div>
                        <span class="font-extrabold text-xl text-white tracking-tight">Poli<span class="text-blue-400">Sehat</span></span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-md">
                        Sistem Informasi Manajemen Poliklinik Terpadu yang menghubungkan pasien, dokter, dan administrasi dalam satu ekosistem digital yang efisien, transparan, dan modern.
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

                {{-- Col 2: Jam Operasional --}}
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm">Jam Operasional</h4>
                    <p class="leading-relaxed">
                        <strong class="text-slate-300 block">Senin - Jumat:</strong> 08:00 - 20:00 WIB
                    </p>
                    <p class="leading-relaxed">
                        <strong class="text-slate-300 block">Sabtu:</strong> 08:00 - 15:00 WIB
                    </p>
                    <p class="text-amber-400 font-semibold">
                        Minggu & Hari Libur: Tutup
                    </p>
                </div>

                {{-- Col 3: Kontak & Lokasi --}}
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm">Hubungi Kami</h4>
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
                        <span>kontak@poliklinik-sehat.id</span>
                    </p>
                </div>

            </div>

            {{-- Copyright --}}
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} Poliklinik Sehat. Seluruh Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-slate-300 transition">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-slate-300 transition">Syarat & Ketentuan</a>
                    <a href="{{ route('login') }}" class="hover:text-slate-300 transition">Portal Staf</a>
                </div>
            </div>

        </div>
    </footer>

</body>
</html>
