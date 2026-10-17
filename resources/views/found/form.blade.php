@extends('layouts.found')
@php $editing = $item->exists; @endphp
@section('title', $editing ? 'Ubah laporan' : 'Tambah laporan')
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('found.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">← Daftar barang temuan</a>
    <h1 class="mt-5 font-display text-3xl font-extrabold text-ink">{{ $editing ? 'Ubah laporan' : 'Laporkan barang temuan' }}</h1>
    <p class="mt-2 mb-6 text-sm text-slate-500">Ceritakan ciri barang dan tempat kamu menemukannya agar pemiliknya mudah mengenali.</p>
    @if ($categories->isEmpty())
        <div role="status" class="mb-5 p-4 rounded-lg bg-amber-50 text-amber-900 border border-amber-200">Kategori belum tersedia. Hubungi pengelola untuk menambahkan kategori sebelum menyimpan laporan.</div>
    @endif
    <form method="POST" action="{{ $editing ? route('found.update', $item) : route('found.store') }}" enctype="multipart/form-data" class="panel p-5 sm:p-8 space-y-5">
        @csrf
        @if ($editing) @method('PUT') @endif
        <p class="text-xs text-slate-500">Semua kolom wajib diisi kecuali foto.</p>
        <div>
            <label for="title" class="label">Nama barang</label>
            <input id="title" name="title" class="input" value="{{ old('title', $item->title) }}" maxlength="150" placeholder="Contoh: Dompet kulit cokelat" required>
        </div>
        <div>
            <label for="category_id" class="label">Kategori</label>
            <select id="category_id" name="category_id" class="input" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $item->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="description" class="label">Ciri dan deskripsi barang</label>
            <textarea id="description" name="description" rows="5" maxlength="5000" class="input" placeholder="Warna, merek, isi, atau ciri khusus barang" required>{{ old('description', $item->description) }}</textarea>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="location" class="label">Lokasi ditemukan</label>
                <input id="location" name="location" class="input" value="{{ old('location', $item->location) }}" maxlength="150" required>
            </div>
            <div>
                <label for="event_date" class="label">Tanggal ditemukan</label>
                <input id="event_date" name="event_date" type="date" class="input" value="{{ old('event_date', $item->event_date?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" required>
            </div>
        </div>
        <div>
            <label for="contact" class="label">Nomor kontak</label>
            <input id="contact" name="contact" type="tel" class="input" value="{{ old('contact', $item->contact) }}" pattern="\+?[0-9]{8,15}" maxlength="16" placeholder="08xxxxxxxxxx" aria-describedby="contact-help" required>
            <p id="contact-help" class="mt-2 text-xs text-slate-500">8–15 angka, boleh diawali +. Nomor ini akan tampil di detail laporan.</p>
        </div>
        @if ($editing)
            <div>
                <label for="status" class="label">Status laporan</label>
                <select id="status" name="status" class="input" required>
                    @foreach (\App\Models\FoundItem::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $item->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div>
            <label for="photo" class="label">Foto barang (opsional)</label>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="input" aria-describedby="photo-help">
            <p id="photo-help" class="mt-2 text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB. Setelah validasi gagal, pilih ulang foto.</p>
            <img id="photo-preview" @if ($item->photo_url) src="{{ $item->photo_url }}" @endif alt="Pratinjau foto barang" class="mt-4 w-40 h-40 rounded-xl border object-cover {{ $item->photo_url ? '' : 'hidden' }}">
            <p id="photo-error" role="alert" class="mt-2 text-sm text-red-700"></p>
            @if ($editing && $item->photo)
                <label class="mt-3 flex items-center gap-2 text-sm"><input type="checkbox" name="remove_photo" value="1" @checked(old('remove_photo'))> Hapus foto saat ini</label>
                <p class="mt-1 text-xs text-slate-500">Jika memilih foto baru, foto baru akan menggantikan foto lama.</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="btn-primary disabled:opacity-50" @disabled($categories->isEmpty())>{{ $editing ? 'Simpan perubahan' : 'Kirim laporan' }}</button>
            <a href="{{ $editing ? route('found.show', $item) : route('found.index') }}" class="btn-line">Batal</a>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
    const photoInput = document.getElementById('photo');
    const preview = document.getElementById('photo-preview');
    const originalPhoto = preview.getAttribute('src');
    const photoError = document.getElementById('photo-error');
    let previewUrl;
    photoInput.addEventListener('change', () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        photoError.textContent = '';
        const file = photoInput.files[0];
        if (file && (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024)) {
            photoError.textContent = 'Pilih foto JPG, PNG, atau WebP maksimal 2 MB.';
            photoInput.value = '';
        }
        if (photoInput.files[0]) {
            previewUrl = URL.createObjectURL(photoInput.files[0]);
            preview.src = previewUrl;
            preview.classList.remove('hidden');
        } else {
            if (originalPhoto) preview.src = originalPhoto;
            else preview.removeAttribute('src');
            preview.classList.toggle('hidden', !originalPhoto);
        }
    });
</script>
@endpush