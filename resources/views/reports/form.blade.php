@extends('layouts.app')
@php
    $editing = $item !== null;
    $action = $editing ? route($meta['key'].'.update', $item->id) : route($meta['key'].'.store');
@endphp
@section('title', ($editing ? 'Ubah ' : 'Tambah ').$meta['title'])
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ $editing ? route($meta['key'].'.show', $item->id) : route($meta['key'].'.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">Kembali</a>
    <h1 class="font-display font-extrabold text-3xl text-ink mt-2 mb-6">{{ $editing ? 'Ubah laporan' : 'Laporan baru' }}: {{ strtolower($meta['title']) }}</h1>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="panel p-6 md:p-8 grid gap-5 md:grid-cols-2">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="md:col-span-2">
            <label for="title" class="label">Nama barang</label>
            <input id="title" name="title" value="{{ old('title', $item?->title) }}" required maxlength="120" class="input" placeholder="Contoh: Dompet kulit cokelat">
        </div>
        <div>
            <label for="category_id" class="label">Kategori</label>
            <select id="category_id" name="category_id" required class="input">
                <option value="">Pilih kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected((int) old('category_id', $item?->category_id) === $c->id)>{{ $c->icon }} {{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="event_date" class="label">{{ $meta['dateLabel'] }}</label>
            <input id="event_date" type="date" name="event_date" required max="{{ now()->toDateString() }}" value="{{ old('event_date', $item?->event_date?->toDateString()) }}" class="input">
        </div>
        <div class="md:col-span-2">
            <label for="location" class="label">Lokasi kejadian</label>
            <input id="location" name="location" value="{{ old('location', $item?->location) }}" required maxlength="150" class="input" placeholder="Contoh: Perpustakaan Pusat, lantai 2">
        </div>
        <div class="md:col-span-2">
            <label for="description" class="label">Ciri-ciri dan keterangan</label>
            <textarea id="description" name="description" rows="4" required maxlength="2000" class="input" placeholder="Warna, merek, isi, tanda khusus, dan hal lain yang membantu">{{ old('description', $item?->description) }}</textarea>
        </div>
        <div>
            <label for="contact" class="label">Nomor WhatsApp yang bisa dihubungi</label>
            <input id="contact" name="contact" value="{{ old('contact', $item?->contact ?? auth()->user()->phone) }}" required class="input" placeholder="08xxxxxxxxxx" inputmode="tel">
        </div>
        @if ($editing)
            <div>
                <label for="status" class="label">Status laporan</label>
                <select id="status" name="status" class="input">
                    @foreach ($meta['statuses'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('status', $item->status) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="md:col-span-2">
            <label for="photo" class="label">Foto barang <span class="font-medium text-slate-400">(opsional, JPG/PNG/WebP, maks. 2 MB)</span></label>
            <div class="flex items-start gap-4">
                <img id="preview" src="{{ $item?->photo_url }}" alt="Pratinjau foto" class="w-28 h-28 rounded-lg object-cover border border-slate-200 {{ $item?->photo_url ? '' : 'hidden' }}">
                <div class="flex-1">
                    <input id="photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp" class="input file:mr-3 file:rounded-md file:border-0 file:bg-ink file:text-white file:px-3 file:py-1.5 file:text-xs file:font-bold">
                    @if ($editing && $item->photo)
                        <label class="mt-2 flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remove_photo" value="1" class="rounded border-slate-300"> Hapus foto saat ini</label>
                    @endif
                </div>
            </div>
        </div>
        <div class="md:col-span-2 flex gap-3 pt-2">
            <button type="submit" class="btn-primary">{{ $editing ? 'Simpan perubahan' : 'Kirim laporan' }}</button>
            <a href="{{ $editing ? route($meta['key'].'.show', $item->id) : route($meta['key'].'.index') }}" class="btn-line">Batal</a>
        </div>
    </form>
</div>
@push('scripts')
<script>
    document.getElementById('photo').addEventListener('change', e => {
        const f = e.target.files[0], img = document.getElementById('preview');
        if (f) { img.src = URL.createObjectURL(f); img.classList.remove('hidden'); }
    });
</script>
@endpush
@endsection
