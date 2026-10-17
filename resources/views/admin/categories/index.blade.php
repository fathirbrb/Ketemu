@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('content')
<h1 class="font-display font-extrabold text-4xl text-ink mb-6">Kategori barang</h1>
@include('admin._nav')

<div class="grid lg:grid-cols-3 gap-6 items-start">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="panel p-5 space-y-4">
        @csrf
        <h2 class="font-display font-extrabold text-lg text-ink">Tambah kategori</h2>
        <div>
            <label for="name" class="label">Nama kategori</label>
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="60" class="input" placeholder="Contoh: Perhiasan">
        </div>
        <div>
            <label for="icon" class="label">Emoji <span class="font-medium text-slate-400">(opsional)</span></label>
            <input id="icon" name="icon" value="{{ old('icon') }}" maxlength="8" class="input" placeholder="💍">
        </div>
        <button type="submit" class="btn-primary w-full">Simpan kategori</button>
    </form>

    <div class="lg:col-span-2 panel overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b border-slate-200">
                <tr><th class="px-4 py-3 font-bold">Kategori</th><th class="px-4 py-3 font-bold">Laporan</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $c)
                    <tr>
                        <td class="px-4 py-3 font-bold text-ink">{{ $c->icon }} {{ $c->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $c->lost_items_count }} hilang, {{ $c->found_items_count }} temuan</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $c->id) }}" class="btn-line !px-3 !py-1.5">Ubah</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $c->id) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-1.5">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">Belum ada kategori. Tambahkan lewat formulir di samping.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
