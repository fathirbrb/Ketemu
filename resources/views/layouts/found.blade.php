<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') | Ketemu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: {
                    sans: ['"DM Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Bricolage Grotesque"', '"DM Sans"', 'sans-serif'],
                },
                colors: { ink: '#17233B', tag: '#F5B82E', lost: '#E4572E', found: '#1F8A70', paper: '#F3F5F8' },
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
</head>
<body class="bg-paper text-slate-800 font-sans antialiased min-h-screen flex flex-col">
@php
    $links = [
        ['lost.index', 'Barang Hilang', 'lost.*'],
        ['found.index', 'Barang Temuan', 'found.*'],
    ];
@endphp

<header class="bg-ink text-white sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="{{ route('found.index') }}" class="flex items-center gap-2.5 font-display font-extrabold text-2xl tracking-tight">
            <span class="relative inline-block w-7 h-7 bg-tag rounded-md rotate-6" aria-hidden="true">
                <span class="absolute top-1.5 left-1.5 w-2 h-2 rounded-full bg-ink"></span>
            </span>
            Ketemu
        </a>
        <button id="menuBtn" type="button" class="md:hidden p-2 rounded-lg hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-tag" aria-label="Buka menu" aria-expanded="false">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <nav id="menu" class="hidden md:flex absolute md:static top-16 inset-x-0 bg-ink md:bg-transparent flex-col md:flex-row md:items-center gap-1 p-3 md:p-0 text-sm font-bold border-t border-white/10 md:border-0">
            @foreach ($links as [$route, $label, $pattern])
                <a href="{{ route($route) }}" class="px-3 py-2 rounded-lg {{ request()->routeIs($pattern) ? 'bg-white/15 text-tag' : 'text-white/80 hover:text-white hover:bg-white/10' }}">{{ $label }}</a>
            @endforeach

        </nav>
    </div>
</header>

<main class="flex-1 w-full max-w-6xl mx-auto px-4 py-8">
    @if (session('success'))
    <div role="status" class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div role="alert" class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div role="alert" class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @yield('content')
</main>

<footer class="border-t border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-slate-500 flex flex-wrap justify-between gap-2">
        <p><span class="font-display font-extrabold text-ink">Ketemu</span>, sistem informasi barang hilang dan temuan.</p>
        <p>UTP Pemrograman Web Lanjut {{ date('Y') }}</p>
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
