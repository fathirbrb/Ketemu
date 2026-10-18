@php
    $statuses = $item::STATUSES;
    $done = $item->status === array_key_last($statuses);
@endphp
<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold leading-4 {{ $done ? 'bg-found/15 text-found' : 'bg-tag/30 text-amber-800' }}">{{ $statuses[$item->status] ?? $item->status }}</span>
