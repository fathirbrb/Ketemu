@extends('layouts.admin')
@php
    $isLost = $meta['key'] === 'lost';
    $wa = preg_replace('/\D/', '', $item->contact);
    if (str_starts_with($wa, '0')) { $wa = '62'.substr($wa, 1); }
@endphp
@section('title', $item->title)
@section('content')
<a href="{{ route($meta['route'].'.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">Kembali ke {{ strtolower($meta['title']) }}</a>

<div class="mt-4 grid lg:grid-cols-5 gap-6">
    <div class="lg:col-span-3 space-y-5">
        <div class="panel overflow-hidden">
            <div class="h-1.5 {{ $isLost ? 'bg-lost' : 'bg-found' }}"></div>
            @if ($item->photo_url)
                <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" class="w-full max-h-96 object-cover">
            @else
                <div class="h-44 grid place-items-center text-6xl bg-slate-100" aria-hidden="true">{{ $item->category->icon ?? '📦' }}</div>
            @endif
        </div>
        <div class="panel p-5">
            <h2 class="font-display font-extrabold text-lg text-ink mb-2">Ciri-ciri dan keterangan</h2>
            <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $item->description }}</p>
        </div>
    </div>

    <aside class="lg:col-span-2 space-y-4">
        <div class="panel p-5">
            <p class="text-sm font-bold {{ $isLost ? 'text-lost' : 'text-found' }}">{{ $meta['title'] }} · {{ $item->category->name }}</p>
            <h1 class="font-display font-extrabold text-2xl leading-tight text-ink mt-1">{{ $item->title }}</h1>
            <div class="mt-3">@include('admin._badge')</div>
            <dl class="mt-5 space-y-3 text-sm">
                <div><dt class="text-slate-400">Lokasi</dt><dd class="font-bold text-ink">{{ $item->location }}</dd></div>
                <div><dt class="text-slate-400">{{ $meta['dateLabel'] }}</dt><dd class="font-bold text-ink">{{ $item->event_date->translatedFormat('l, d F Y') }}</dd></div>
                <div><dt class="text-slate-400">Dilaporkan oleh</dt><dd class="font-bold text-ink">{{ $item->user->name }} ({{ $item->user->email }})</dd></div>
                <div><dt class="text-slate-400">Kontak</dt><dd class="font-bold text-ink">{{ $item->contact }}</dd></div>
                <div><dt class="text-slate-400">Dibuat</dt><dd class="font-bold text-ink">{{ $item->created_at->translatedFormat('d F Y, H:i') }}</dd></div>
            </dl>
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn-tag w-full mt-5">Hubungi lewat WhatsApp</a>
        </div>
        <div class="panel p-4 flex gap-3">
            <a href="{{ route($meta['route'].'.edit', $item->id) }}" class="btn-line flex-1">Ubah</a>
            <form method="POST" action="{{ route($meta['route'].'.destroy', $item->id) }}" class="flex-1" onsubmit="return confirm('Hapus laporan ini? Tindakan ini tidak bisa dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger w-full">Hapus</button>
            </form>
        </div>
    </aside>
</div>
@endsection
