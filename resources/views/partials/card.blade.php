@php $isLost = $meta['key'] === 'lost'; @endphp
<a href="{{ route($meta['key'].'.show', $item->id) }}" class="report-card group relative flex flex-col bg-white rounded-xl border border-slate-200 overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-ink hover:border-ink/40 transition-colors">
    <div class="h-1.5 {{ $isLost ? 'bg-lost' : 'bg-found' }}"></div>
    <div class="report-image aspect-[4/3] bg-slate-100 overflow-hidden">
        @if ($item->photo_url)
            <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" loading="lazy" class="w-full h-full object-cover">
        @else
            <div class="report-placeholder w-full h-full flex flex-col items-center justify-center gap-3 text-5xl bg-slate-100"><span aria-hidden="true">{{ $item->category->icon ?? '📦' }}</span><span class="text-xs font-medium text-slate-500">Foto belum ditambahkan</span></div>
        @endif
    </div>
    <div class="p-4 flex-1 flex flex-col gap-1.5">
        <div class="flex items-center justify-between gap-2 text-xs">
            <span class="font-bold {{ $isLost ? 'text-lost' : 'text-found' }}">{{ $isLost ? 'Hilang' : 'Ditemukan' }} · {{ $item->category->name }}</span>
            @include('partials.badge')
        </div>
        <h3 class="font-display font-extrabold text-lg leading-snug text-ink line-clamp-2">{{ $item->title }}</h3>
        <p class="text-sm text-slate-500 line-clamp-1">{{ $item->location }}</p>
        <p class="text-xs text-slate-400 mt-auto pt-2 border-t border-dashed border-slate-200">{{ $item->event_date->translatedFormat('d F Y') }}</p>
    </div>
</a>
