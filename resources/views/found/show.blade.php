@extends('layouts.found')
@section('title', $item->title)
@section('content')
<a href="{{ route('found.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">← Daftar barang temuan</a>
<div class="mt-5 grid lg:grid-cols-2 gap-6 items-start">
    <div class="panel overflow-hidden">
        @if ($item->photo_url)
            <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" class="w-full max-h-[36rem] object-contain bg-slate-100">
        @else
            <div class="aspect-[4/3] bg-slate-100 flex flex-col items-center justify-center gap-4">
                <span class="text-6xl" aria-hidden="true">{{ $item->category?->icon ?? '📦' }}</span>
                <p class="text-sm text-slate-500">Foto belum ditambahkan</p>
            </div>
        @endif
    </div>
    <article class="panel p-5 sm:p-8">
        <div class="flex flex-wrap items-center gap-3 text-xs font-bold">
            <span class="text-found">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
            <span class="rounded-full px-3 py-1 {{ $item->status === 'diklaim' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ \App\Models\FoundItem::STATUSES[$item->status] ?? $item->status }}</span>
        </div>
        <h1 class="mt-4 font-display text-3xl font-extrabold text-ink break-words">{{ $item->title }}</h1>
        <dl class="mt-6 space-y-4 text-sm">
            <div><dt class="font-bold text-ink">Lokasi ditemukan</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->location }}</dd></div>
            <div><dt class="font-bold text-ink">Tanggal ditemukan</dt><dd class="mt-1 text-slate-600">{{ $item->event_date->format('d/m/Y') }}</dd></div>
            <div><dt class="font-bold text-ink">Pelapor</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->user?->name ?? 'Pengguna' }}</dd></div>
            <div><dt class="font-bold text-ink">Ciri dan deskripsi barang</dt><dd class="mt-1 text-slate-600 whitespace-pre-line break-words">{{ $item->description }}</dd></div>
            <div><dt class="font-bold text-ink">Nomor kontak penemu</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->contact }}</dd></div>
        </dl>
        @php $phoneNumber = preg_replace('/[^0-9+]/', '', $item->contact); @endphp
        @if (preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber))
            <a href="tel:{{ $phoneNumber }}" class="btn-tag mt-6">Hubungi penemu</a>
        @endif
        @if (auth()->check() && (int) auth()->id() === (int) $item->user_id)
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-3">
                <a href="{{ route('found.edit', $item) }}" class="btn-line">Ubah laporan</a>
                <form method="POST" action="{{ route('found.destroy', $item) }}" onsubmit="return confirm('Hapus laporan ini? Laporan yang dihapus tidak bisa dikembalikan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Hapus laporan</button>
                </form>
            </div>
        @endif
    </article>
</div>
@endsection