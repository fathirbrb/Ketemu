<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Admin') | Ketemu Admin</title>
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
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body class="admin-ui bg-paper text-slate-800 font-sans antialiased">
@php
    $nav = [
        ['admin.dashboard', 'Ringkasan', 'admin.dashboard', '<path d="M3 12l9-9 9 9M5 10v10h14V10"/>'],
        ['admin.lost.index', 'Barang hilang', 'admin.lost.*', '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>'],
        ['admin.found.index', 'Barang temuan', 'admin.found.*', '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>'],
        ['admin.categories.index', 'Kategori', 'admin.categories.*', '<path d="M3 12V4h8l10 10-8 8L3 12z"/><circle cx="7.5" cy="8.5" r="1.2"/>'],
        ['admin.users.index', 'Pengguna', 'admin.users.*', '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.5 3.3-5 6.5-5s5.7 1.5 6.5 5M17 5a3 3 0 010 6M18 15c2 .5 3.2 2 3.7 5"/>'],
    ];
@endphp
<div class="min-h-screen lg:flex">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 bg-ink text-white flex flex-col transition-transform duration-200">
        <a href="{{ route('admin.dashboard') }}" class="admin-logo flex items-center gap-2.5 px-6 h-16 border-b border-white/10" aria-label="Ketemu — Panel admin">
            <span class="relative block w-36 h-10 overflow-hidden"><img src="{{ asset('images/ketemu-logo-navy.png') }}" alt="Ketemu" class="absolute max-w-none w-[230px] left-[-54px] top-1/2 -translate-y-1/2"></span>
        </a>
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-1 text-sm font-bold">
            <p class="px-3 pb-2 text-xs font-medium text-white/40">Menu admin</p>
            @foreach ($nav as [$route, $label, $pattern, $icon])
                @php $active = request()->routeIs($pattern); @endphp
                <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif class="flex items-center gap-3 rounded-lg px-3 py-2.5 border-l-4 {{ $active ? 'bg-tag text-ink border-tag' : 'border-transparent text-white/75 hover:bg-white/5 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-4 text-sm">
            <p class="font-bold truncate">{{ auth()->user()->name }}</p>
            <p class="text-white/50 text-xs truncate">{{ auth()->user()->email }}</p>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('home') }}" class="flex-1 text-center rounded-lg border border-white/20 px-3 py-2 font-bold hover:bg-white/10">Lihat situs</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-white/10 px-3 py-2 font-bold hover:bg-white/20">Keluar</button>
                </form>
            </div>
        </div>
    </aside>
    <div id="overlay" class="fixed inset-0 z-30 bg-black/50 hidden lg:hidden"></div>

    <div class="flex-1 min-w-0 flex flex-col">
        <header class="lg:hidden sticky top-0 z-20 h-14 bg-ink text-white flex items-center gap-3 px-4">
            <button id="sidebarBtn" type="button" class="p-2 -ml-2 rounded-lg hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-tag" aria-label="Buka menu admin">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <span class="font-display font-extrabold text-lg">Panel Admin</span>
        </header>
        <main class="w-full max-w-6xl mx-auto p-4 sm:p-8">
            @include('admin._flash')
            @yield('content')
        </main>
    </div>
</div>
<script>
    const sb = document.getElementById('sidebar'), ov = document.getElementById('overlay');
    const toggle = () => { sb.classList.toggle('-translate-x-full'); ov.classList.toggle('hidden'); };
    document.getElementById('sidebarBtn').addEventListener('click', toggle);
    ov.addEventListener('click', toggle);
</script>
@stack('scripts')
</body>
</html>
