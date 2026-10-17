@extends('layouts.app')
@section('title', 'Panel Admin')
@section('content')
<h1 class="font-display font-extrabold text-4xl text-ink mb-2">Panel Admin</h1>
<p class="text-slate-500 mb-6">Pantau laporan, kategori, dan pengguna. Admin bisa mengubah atau menghapus laporan siapa pun dari halaman detail laporan.</p>
@include('admin._nav')

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    @foreach ([['Pengguna', $stats['users']], ['Kategori', $stats['categories']], ['Laporan hilang', $stats['lost']], ['Laporan temuan', $stats['found']]] as [$label, $n])
        <div class="panel p-5">
            <p class="font-display font-extrabold text-4xl text-ink">{{ $n }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $label }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6">
    @foreach ([['lost', 'Laporan hilang terbaru', $latestLost], ['found', 'Laporan temuan terbaru', $latestFound]] as [$key, $title, $list])
        <section class="panel overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-display font-extrabold text-lg text-ink">{{ $title }}</h2>
                <a href="{{ route($key.'.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">Lihat semua</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($list as $item)
                    <li class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route($key.'.show', $item->id) }}" class="font-bold text-ink hover:underline line-clamp-1">{{ $item->title }}</a>
                            <p class="text-xs text-slate-500">{{ $item->user->name }} · {{ $item->event_date->translatedFormat('d M Y') }}</p>
                        </div>
                        @include('partials.badge')
                    </li>
                @empty
                    <li class="px-5 py-6 text-sm text-slate-500">Belum ada laporan.</li>
                @endforelse
            </ul>
        </section>
    @endforeach
</div>
@endsection
