<div class="flex flex-wrap gap-2 mb-8">
    @foreach ([['admin.dashboard', 'Ringkasan', 'admin.dashboard'], ['admin.categories.index', 'Kategori', 'admin.categories.*'], ['admin.users.index', 'Pengguna', 'admin.users.*'], ['lost.index', 'Kelola barang hilang', 'lost.*'], ['found.index', 'Kelola barang temuan', 'found.*']] as [$route, $label, $pattern])
        <a href="{{ route($route) }}" class="rounded-lg px-4 py-2 text-sm font-bold {{ request()->routeIs($pattern) ? 'bg-ink text-white' : 'bg-white border border-slate-200 text-ink hover:bg-slate-50' }}">{{ $label }}</a>
    @endforeach
</div>
