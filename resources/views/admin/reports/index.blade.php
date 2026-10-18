@extends('layouts.admin')
@section('title', 'Kelola '.$meta['title'])
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-ink">{{ $meta['title'] }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $items->total() }} laporan. {{ $meta['desc'] }}</p>
    </div>
    <a href="{{ route($meta['route'].'.create') }}" class="btn-tag">Tambah laporan</a>
</div>

<form method="GET" class="panel p-4 grid gap-3 md:grid-cols-12 mb-6">
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
    <div class="md:col-span-2 flex gap-2">
        <button type="submit" class="btn-primary flex-1">Cari</button>
        <a href="{{ route($meta['route'].'.index') }}" class="btn-line">Reset</a>
    </div>
</form>

<div class="panel overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-slate-500 border-b border-slate-200">
            <tr>
                <th scope="col" class="px-4 py-3 font-bold">Barang</th>
                <th scope="col" class="px-4 py-3 font-bold">Lokasi</th>
                <th scope="col" class="px-4 py-3 font-bold">{{ $meta['dateLabel'] }}</th>
                <th scope="col" class="px-4 py-3 font-bold">Pelapor</th>
                <th scope="col" class="px-4 py-3 font-bold">Status</th>
                <th scope="col" class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($items as $item)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3 min-w-[12rem]">
                            @if ($item->photo_url)
                                <img src="{{ $item->photo_url }}" alt="" class="w-11 h-11 rounded-lg object-cover shrink-0">
                            @else
                                <span class="w-11 h-11 rounded-lg bg-slate-100 grid place-items-center text-xl shrink-0" aria-hidden="true">{{ $item->category->icon ?? '📦' }}</span>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route($meta['route'].'.show', $item->id) }}" class="font-bold text-ink hover:underline line-clamp-1">{{ $item->title }}</a>
                                <p class="text-xs text-slate-500">{{ $item->category->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $item->location }}</td>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $item->event_date->translatedFormat('d M Y') }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $item->user->name }}</td>
                    <td class="px-4 py-3">@include('admin._badge')</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route($meta['route'].'.show', $item->id) }}" class="btn-line !px-3 !py-1.5">Lihat</a>
                            <a href="{{ route($meta['route'].'.edit', $item->id) }}" class="btn-line !px-3 !py-1.5">Ubah</a>
                            <form method="POST" action="{{ route($meta['route'].'.destroy', $item->id) }}" onsubmit="return confirm('Hapus laporan ini? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada laporan yang cocok dengan pencarian ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $items->links() }}</div>
@endsection
