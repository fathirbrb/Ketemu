@extends('layouts.found')
@section('title', $item->title)
@section('content')
<div class="report-detail">
<a href="{{ route('found.index') }}" class="detail-breadcrumb">Daftar barang temuan</a>
<div class="detail-grid">
    <div class="detail-photo">
        @if ($item->photo_url)
            <a href="{{ $item->photo_url }}" target="_blank" rel="noopener" class="detail-photo-link focus:outline-none focus-visible:ring-2 focus-visible:ring-ink" title="Lihat foto ukuran penuh">
                <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" class="detail-photo-image">
            </a>
        @else
            <div class="detail-photo-empty flex flex-col items-center justify-center gap-4">
                <span class="text-6xl" aria-hidden="true">{{ $item->category?->icon ?? '📦' }}</span>
                <p class="text-sm text-slate-500">Foto belum ditambahkan</p>
            </div>
        @endif
    </div>
    <article class="detail-information">
        <div class="flex flex-wrap items-center gap-3 text-xs font-bold">
            <span class="text-found">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
            <span class="rounded-full px-3 py-1 {{ $item->status === 'diklaim' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ \App\Models\FoundItem::STATUSES[$item->status] ?? $item->status }}</span>
        </div>
        <h1 class="detail-title break-words">{{ $item->title }}</h1>
        <dl class="detail-facts">
            <div><dt class="font-bold text-ink">Lokasi ditemukan</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->location }}</dd></div>
            <div><dt class="font-bold text-ink">Tanggal ditemukan</dt><dd class="mt-1 text-slate-600">{{ $item->event_date->format('d/m/Y') }}</dd></div>
            <div><dt class="font-bold text-ink">Pelapor</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->user?->name ?? 'Pengguna' }}</dd></div>
            <div><dt class="font-bold text-ink">Nomor kontak penemu</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->contact }}</dd></div>
        </dl>
        @php $phoneNumber = preg_replace('/[^0-9+]/', '', $item->contact); @endphp
        @php $isOwner = auth()->check() && (int) auth()->id() === (int) $item->user_id; @endphp
        @if (preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber) || $isOwner)
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-3">
                @if (preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber))
                    <a href="tel:{{ $phoneNumber }}" class="btn-tag w-full">Hubungi penemu</a>
                @endif
                @if ($isOwner)
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('found.edit', $item) }}" class="btn-line w-full">Ubah laporan</a>
                        <form method="POST" action="{{ route('found.destroy', $item) }}" onsubmit="return confirm('Hapus laporan ini? Laporan yang dihapus tidak bisa dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger w-full">Hapus laporan</button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
        <details class="detail-description" open>
            <summary>Ciri dan deskripsi barang</summary>
            <p class="whitespace-pre-line break-words">{{ $item->description }}</p>
        </details>
    </article>
</div>
</div>
@endsection