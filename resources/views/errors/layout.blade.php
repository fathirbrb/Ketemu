<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} | Ketemu</title>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['"DM Sans"', 'sans-serif'], display: ['"Bricolage Grotesque"', 'sans-serif'] }, colors: { ink: '#17233B', tag: '#F5B82E', paper: '#F3F5F8' } } } };</script>
</head>
<body class="min-h-screen grid place-items-center bg-paper text-slate-800 font-sans p-6">
    <main class="text-center max-w-md">
        <div class="relative mx-auto w-56 bg-tag text-ink rounded-xl -rotate-3 px-6 py-8 shadow-xl">
            <span class="absolute top-3 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-paper"></span>
            <p class="font-display font-extrabold text-7xl leading-none">{{ $code }}</p>
        </div>
        <h1 class="font-display font-extrabold text-3xl text-ink mt-8">{{ $title }}</h1>
        <p class="mt-2 text-slate-600">{{ $message }}</p>
        <div class="mt-6 flex justify-center gap-3">
            <a href="/" class="rounded-lg px-5 py-2.5 text-sm font-bold bg-ink text-white hover:bg-ink/90">Ke beranda</a>
            <a href="javascript:history.back()" class="rounded-lg px-5 py-2.5 text-sm font-bold border border-slate-300 bg-white text-ink hover:bg-slate-50">Halaman sebelumnya</a>
        </div>
    </main>
</body>
</html>
