@extends('layouts.admin')
@php
    $isLost = $meta['key'] === 'lost';
    $wa = preg_replace('/\D/', '', $item->contact);
    if (str_starts_with($wa, '0')) { $wa = '62'.substr($wa, 1); }
@endphp
@section('title', $item->title)
@section('content')
<div class="admin-report-detail">
    <a href="{{ route($meta['route'].'.index') }}" class="admin-detail-back">Kembali ke {{ strtolower($meta['title']) }}</a>
    <div class="admin-detail-grid">
        <div class="admin-detail-photo">
            @if ($item->photo_url)
                <a href="{{ $item->photo_url }}" target="_blank" rel="noopener" title="Lihat foto ukuran penuh">
                    <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}">
                </a>
            @else
                <div class="admin-detail-placeholder">Foto belum ditambahkan</div>
            @endif
        </div>
        <article class="admin-detail-information">
            <div class="flex flex-wrap items-center gap-3">
                <p class="admin-detail-category">{{ $meta['title'] }} · {{ $item->category?->name ?? 'Tanpa kategori' }}</p>
                @include('admin._badge')
            </div>
            <h1>{{ $item->title }}</h1>
            <dl class="admin-detail-facts">
                <div><dt>Lokasi</dt><dd>{{ $item->location }}</dd></div>
                <div><dt>{{ $meta['dateLabel'] }}</dt><dd>{{ $item->event_date->format('d/m/Y') }}</dd></div>
                <div><dt>Dilaporkan oleh</dt><dd>{{ $item->user?->name ?? 'Pengguna' }}<br><span class="text-slate-500">{{ $item->user?->email }}</span></dd></div>
                <div><dt>Nomor kontak</dt><dd>{{ $item->contact }}</dd></div>
                <div><dt>Dibuat</dt><dd>{{ $item->created_at->format('d/m/Y, H:i') }}</dd></div>
            </dl>
            <div class="admin-detail-actions">
                @if (preg_match('/^[0-9]{8,15}$/', $wa))
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn-tag w-full">Hubungi lewat WhatsApp</a>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route($meta['route'].'.edit', $item->id) }}" class="btn-line w-full">Ubah laporan</a>
                    <form method="POST" action="{{ route($meta['route'].'.destroy', $item->id) }}" onsubmit="return confirm('Hapus laporan ini? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger w-full">Hapus laporan</button>
                    </form>
                </div>
            </div>
            <details class="admin-detail-description" open>
                <summary>Ciri dan deskripsi barang</summary>
                <p>{{ $item->description }}</p>
            </details>
        </article>
    </div>
</div>
@endsection