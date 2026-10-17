@extends('layouts.app')
@section('title', 'Ubah Kategori')
@section('content')
<div class="max-w-md mx-auto">
    <a href="{{ route('admin.categories.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">Kembali</a>
    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="panel p-6 space-y-4 mt-3">
        @csrf @method('PUT')
        <h1 class="font-display font-extrabold text-2xl text-ink">Ubah kategori</h1>
        <div>
            <label for="name" class="label">Nama kategori</label>
            <input id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="60" class="input">
        </div>
        <div>
            <label for="icon" class="label">Emoji</label>
            <input id="icon" name="icon" value="{{ old('icon', $category->icon) }}" maxlength="8" class="input">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Simpan perubahan</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-line">Batal</a>
        </div>
    </form>
</div>
@endsection
