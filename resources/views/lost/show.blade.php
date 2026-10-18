@extends('layouts.lost')
@section('title', $item->title)
@section('content')
<a href="{{ route('lost.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">← Daftar barang hilang</a>
<div class="mt-5 grid lg:grid-cols-2 gap-6 items-start">
    <div class="panel overflow-hidden">
        @if ($item->photo_url)
            <a href="{{ $item->photo_url }}" target="_blank" rel="noopener" class="block bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-ink" title="Lihat foto ukuran penuh">
                <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" class="w-full h-72 sm:h-96 object-contain">
            </a>
        @else
            <div class="aspect-[4/3] bg-slate-100 flex flex-col items-center justify-center gap-4">
                <span class="text-6xl" aria-hidden="true">{{ $item->category?->icon ?? '📦' }}</span>
                <p class="text-sm text-slate-500">Foto belum ditambahkan</p>
            </div>
        @endif
    </div>
    <article class="panel p-5 sm:p-8">
        <div class="flex flex-wrap items-center gap-3 text-xs font-bold">
            <span class="text-lost">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
            <span class="rounded-full px-3 py-1 {{ $item->status === 'ditemukan' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ \App\Models\LostItem::STATUSES[$item->status] ?? $item->status }}</span>
        </div>
        <h1 class="mt-4 font-display text-3xl font-extrabold text-ink break-words">{{ $item->title }}</h1>
        <dl class="mt-6 space-y-4 text-sm">
            <div><dt class="font-bold text-ink">Lokasi kehilangan</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->location }}</dd></div>
            <div><dt class="font-bold text-ink">Tanggal hilang</dt><dd class="mt-1 text-slate-600">{{ $item->event_date->format('d/m/Y') }}</dd></div>
            <div><dt class="font-bold text-ink">Pelapor</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->user?->name ?? 'Pengguna' }}</dd></div>
            <div><dt class="font-bold text-ink">Ciri dan deskripsi barang</dt><dd class="mt-1 text-slate-600 whitespace-pre-line break-words">{{ $item->description }}</dd></div>
            <div><dt class="font-bold text-ink">Nomor kontak pelapor</dt><dd class="mt-1 text-slate-600 break-words">{{ $item->contact }}</dd></div>
        </dl>
        @php $phoneNumber = preg_replace('/[^0-9+]/', '', $item->contact); @endphp
        @php $isOwner = auth()->check() && (int) auth()->id() === (int) $item->user_id; @endphp
        @if (preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber) || $isOwner)
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-3">
                @if (preg_match('/^\+?[0-9]{8,15}$/', $phoneNumber))
                    <a href="tel:{{ $phoneNumber }}" class="btn-tag w-full">Hubungi pelapor</a>
                @endif
                @if ($isOwner)
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('lost.edit', $item) }}" class="btn-line w-full">Ubah laporan</a>
                        <form method="POST" action="{{ route('lost.destroy', $item) }}" onsubmit="return confirm('Hapus laporan ini? Laporan yang dihapus tidak bisa dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger w-full">Hapus laporan</button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </article>
</div>
@endsection