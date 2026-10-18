@extends('layouts.lost')

@section('title', 'Barang Hilang')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display font-extrabold text-3xl text-ink">
            Barang Hilang
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            Cari barangmu atau buat laporan kehilangan.
        </p>
    </div>

    @auth
        <a href="{{ route('lost.create') }}" class="btn-tag">
            Tambah laporan
        </a>
    @endauth
</div>

<form method="GET" action="{{ route('lost.index') }}"
      class="panel p-4 mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label for="q" class="label">Cari barang</label>
        <input id="q" name="q" type="search"
               value="{{ request('q') }}"
               placeholder="Nama barang atau lokasi"
               maxlength="150" class="input">
    </div>

    <div>
        <label for="category" class="label">Kategori</label>
        <select id="category" name="category" class="input">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) request('category') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="status" class="label">Status</label>
        <select id="status" name="status" class="input">
            <option value="">Semua status</option>
            @foreach (\App\Models\LostItem::STATUSES as $value => $label)
                <option value="{{ $value }}"
                    @selected(request('status') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary flex-1">Cari</button>
        <a href="{{ route('lost.index') }}" class="btn-line">Reset</a>
    </div>

    @auth
        <label class="flex items-center gap-2 text-sm sm:col-span-2 lg:col-span-4">
            <input type="checkbox" name="mine" value="1"
                   @checked(request()->boolean('mine'))>
            Tampilkan laporan saya saja
        </label>
    @endauth
</form>

<p class="mb-4 text-sm text-slate-500">
    {{ $items->total() }} laporan ditemukan
</p>

<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($items as $item)
        <a href="{{ route('lost.show', $item) }}"
           class="panel overflow-hidden hover:shadow-lg transition
                  focus:outline-none focus-visible:ring-2 focus-visible:ring-ink">
            <div class="relative aspect-[16/10] overflow-hidden bg-[#F2F1ED] flex items-center justify-center">
                @if ($item->photo_url)
                    <img src="{{ $item->photo_url }}"
                         alt="Foto {{ $item->title }}"
                         loading="lazy"
                         class="block absolute inset-0 m-auto w-[65%] h-[65%] object-contain mix-blend-multiply">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center gap-3">
                        <span class="text-5xl" aria-hidden="true">
                            {{ $item->category?->icon ?? '📦' }}
                        </span>
                        <span class="text-xs text-slate-500">
                            Foto belum ditambahkan
                        </span>
                    </div>
                @endif
            </div>

            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-lost">
                        {{ $item->category?->name ?? 'Tanpa kategori' }}
                    </span>

                    <span class="rounded-full px-3 py-1 text-xs font-bold
                        {{ $item->status === 'ditemukan'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-amber-100 text-amber-800' }}">
                        {{ \App\Models\LostItem::STATUSES[$item->status] ?? $item->status }}
                    </span>
                </div>

                <h2 class="mt-3 font-display font-extrabold text-xl text-ink break-words">
                    {{ $item->title }}
                </h2>
                <p class="mt-2 text-sm text-slate-500 break-words">
                    {{ $item->location }}
                </p>
                <p class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                    Hilang pada {{ $item->event_date->format('d/m/Y') }}
                </p>
            </div>
        </a>
    @empty
        <div class="panel p-10 text-center col-span-full">
            <h2 class="font-display font-bold text-xl text-ink">
                Belum ada laporan yang cocok
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Kalau kamu memakai filter, coba ubah kata kunci atau reset pencarian.
            </p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $items->links() }}
</div>
@endsection