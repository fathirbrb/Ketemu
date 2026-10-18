<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') | Ketemu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: {
                    sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
                colors: { ink: '#17233B', tag: '#F5B82E', lost: '#8A6208', found: '#1F8A70', paper: '#F3F5F8' },
            } },
        };
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .label { @apply block text-sm font-bold mb-1.5 text-ink; }
            .input { @apply w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm placeholder-slate-400 focus:outline-none focus:border-ink focus:ring-2 focus:ring-ink/20; }
            .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold bg-ink text-white hover:bg-ink/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-ink transition; }
            .btn-tag { @apply inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold bg-tag text-ink hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-ink transition; }
            .btn-line { @apply inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold border border-slate-300 bg-white text-ink hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-ink transition; }
            .btn-danger { @apply inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold border border-red-200 bg-white text-red-700 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 transition; }
            .panel { @apply bg-white border border-slate-200 rounded-xl; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
</head>
<body class="bg-paper text-slate-800 font-sans antialiased min-h-screen flex flex-col">
@php
    $links = [['home', 'Beranda', 'home'], ['lost.index', 'Barang Hilang', 'lost.*'], ['found.index', 'Barang Temuan', 'found.*']];
@endphp
<header class="bg-ink text-white sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" aria-label="Ketemu — Beranda" class="relative block shrink-0 w-32 sm:w-36 h-10 overflow-hidden rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-tag">
            <img src="{{ asset('images/ketemu-logo-navy.png') }}" alt="Ketemu" class="absolute max-w-none w-[210px] sm:w-[230px] h-auto left-[-49px] sm:left-[-54px] top-1/2 -translate-y-1/2">
        </a>
        <button id="menuBtn" type="button" class="md:hidden p-2 rounded-lg hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-tag" aria-label="Buka menu" aria-expanded="false">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <nav id="menu" class="hidden md:flex absolute md:static top-16 inset-x-0 bg-ink md:bg-transparent flex-col md:flex-row md:items-center gap-1 p-3 md:p-0 text-sm font-bold border-t border-white/10 md:border-0">
            @foreach ($links as [$route, $label, $pattern])
                <a href="{{ route($route) }}" class="px-3 py-2 rounded-lg {{ request()->routeIs($pattern) ? 'bg-white/15 text-tag' : 'text-white/80 hover:text-white hover:bg-white/10' }}">{{ $label }}</a>
            @endforeach
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.*') ? 'bg-white/15 text-tag' : 'text-white/80 hover:text-white hover:bg-white/10' }}">Panel Admin</a>
                @endif
                <span class="md:ml-3 px-3 py-2 text-white/60 font-medium">{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10">Masuk</a>
                <a href="{{ route('register') }}" class="btn-tag md:ml-1">Daftar gratis</a>
            @endauth
        </nav>
    </div>
</header>

<main class="flex-1 w-full {{ request()->routeIs('home') ? '' : 'max-w-6xl mx-auto px-4 py-8' }}">
    @include('partials.flash')
    @yield('content')
</main>

<footer class="site-footer bg-white text-ink border-t border-slate-200">
    <div class="footer-row">
        <nav aria-label="Navigasi footer" class="footer-links">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('lost.index') }}">Barang Hilang</a>
            <a href="{{ route('found.index') }}">Barang Temuan</a>
        </nav>
        <a href="{{ route('home') }}" aria-label="Ketemu — Beranda" class="footer-brand">
            <svg width="24" height="28" viewBox="0 0 64 72" fill="currentColor" class="text-tag" aria-hidden="true">
                <ellipse cx="32" cy="65" rx="25" ry="6" />
                <path fill-rule="evenodd" d="M32 2C16.5 2 5 13.4 5 28c0 13.8 17 32 24.1 38.2a4.4 4.4 0 0 0 5.8 0C42 60 59 41.8 59 28 59 13.4 47.5 2 32 2Zm0 15a10 10 0 1 0 0 20 10 10 0 0 0 0-20Z" clip-rule="evenodd" />
            </svg>
            <span>Ketemu</span>
        </a>
        <p class="footer-copyright">&copy; {{ date('Y') }} Ketemu. All rights reserved.</p>
    </div>
</footer>

<script>
    const btn = document.getElementById('menuBtn'), menu = document.getElementById('menu');
    btn.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden') === false;
        btn.setAttribute('aria-expanded', open);
    });
</script>
@stack('scripts')
</body>
</html>
