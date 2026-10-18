{{-- Dipakai beranda. Butuh: $item (LostItem/FoundItem) dan $key ('lost' | 'found') --}}
@php
    $model = $key === 'lost' ? \App\Models\LostItem::class : \App\Models\FoundItem::class;
    $done = in_array($item->status, ['ditemukan', 'diklaim'], true);
    $accent = $key === 'lost' ? 'text-lost' : 'text-found';
    $dateLabel = $key === 'lost' ? 'Hilang pada' : 'Ditemukan pada';
@endphp
<a href="{{ route($key.'.show', $item) }}"
   class="panel overflow-hidden hover:shadow-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-ink">
    <div class="relative aspect-[16/10] overflow-hidden bg-[#F2F1ED] flex items-center justify-center">
        @if ($item->photo_url)
            <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" loading="lazy" class="block absolute inset-0 m-auto w-[65%] h-[65%] object-contain mix-blend-multiply">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center gap-3">
                <span class="text-5xl" aria-hidden="true">{{ $item->category?->icon ?? '📦' }}</span>
                <span class="text-xs text-slate-500">Foto belum ditambahkan</span>
            </div>
        @endif
    </div>
    <div class="p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="text-xs font-bold {{ $accent }}">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $done ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $model::STATUSES[$item->status] ?? $item->status }}
            </span>
        </div>
        <h3 class="mt-3 font-display font-extrabold text-xl text-ink break-words">{{ $item->title }}</h3>
        <p class="mt-2 text-sm text-slate-500 break-words">{{ $item->location }}</p>
        <p class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">{{ $dateLabel }} {{ $item->event_date->format('d/m/Y') }}</p>
    </div>
</a>
