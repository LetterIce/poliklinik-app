<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - Poliklinik' : 'Poliklinik' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen">

    <div class="app-wrapper relative">
        {{-- Mobile Overlay --}}
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="sidebar-overlay"></div>

        {{-- Sidebar --}}
        <x-partials.sidebar />

        {{-- Main Container --}}
        <div class="main-content">
            {{-- Header Navbar --}}
            <x-partials.header :title="$title ?? 'Dashboard'" />

            {{-- Main Content Area --}}
            <main class="main-scroll">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <x-partials.footer />
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                overlay.style.display = 'none';
            } else {
                sidebar.classList.add('open');
                overlay.style.display = 'block';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>