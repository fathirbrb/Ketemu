@extends('layouts.app')
@section('title', $meta['title'])
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display font-extrabold text-4xl text-ink">{{ $meta['title'] }}</h1>
        <p class="text-slate-500 mt-1">{{ $meta['desc'] }}</p>
    </div>
    <a href="{{ route($meta['key'].'.create') }}" class="btn-tag">Tambah laporan</a>
</div>

<form method="GET" class="panel p-4 grid gap-3 md:grid-cols-12 mb-8">
    <div class="md:col-span-4">
        <label for="q" class="sr-only">Cari</label>
        <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama barang atau lokasi" class="input">
    </div>
    <div class="md:col-span-3">
        <label for="category" class="sr-only">Kategori</label>
        <select id="category" name="category" class="input">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((int) request('category') === $c->id)>{{ $c->icon }} {{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-3">
        <label for="status" class="sr-only">Status</label>
        <select id="status" name="status" class="input">
            <option value="">Semua status</option>
            @foreach ($meta['statuses'] as $val => $label)
                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-primary md:col-span-2">Cari</button>
    @auth
        <label class="md:col-span-12 flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="mine" value="1" @checked(request()->boolean('mine')) class="rounded border-slate-300 text-ink focus:ring-ink">
            Tampilkan laporan saya saja
        </label>
    @endauth
</form>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($items as $item)
        @include('partials.card')
    @empty
        <div class="panel p-10 text-center col-span-full">
            <p class="font-display font-extrabold text-xl text-ink">Belum ada laporan yang cocok</p>
            <p class="text-slate-500 mt-1">Coba ubah kata kunci atau filter, atau buat laporan baru.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">{{ $items->links() }}</div>
@endsection
