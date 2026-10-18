@extends('layouts.admin')
@section('title', 'Ringkasan')
@section('content')
<h1 class="font-display font-extrabold text-2xl sm:text-3xl text-ink">Ringkasan</h1>
<p class="text-sm text-slate-500 mt-1 mb-6">Pantau laporan, kategori, dan pengguna Ketemu dari satu tempat.</p>

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach ([['Pengguna', $stats['users'], 'admin.users.index'], ['Kategori', $stats['categories'], 'admin.categories.index'], ['Laporan hilang', $stats['lost'], 'admin.lost.index'], ['Laporan temuan', $stats['found'], 'admin.found.index']] as [$label, $n, $route])
        <a href="{{ route($route) }}" class="panel p-5 hover:border-ink/40 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-ink">
            <p class="font-display font-extrabold text-4xl text-ink">{{ $n }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $label }}</p>
        </a>
    @endforeach
</div>

<div class="panel p-5 mt-4">
    <h2 class="font-display font-extrabold text-lg text-ink mb-4">Status laporan</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($status as $label => $n)
            <div class="border-l-4 {{ in_array($label, ['Sudah ditemukan', 'Sudah diklaim']) ? 'border-found' : 'border-tag' }} pl-3">
                <p class="font-display font-extrabold text-2xl text-ink">{{ $n }}</p>
                <p class="text-sm text-slate-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="grid xl:grid-cols-2 gap-6 mt-6">
    @foreach ([['lost', 'Laporan hilang terbaru', $latestLost], ['found', 'Laporan temuan terbaru', $latestFound]] as [$key, $title, $list])
        <section class="panel overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-display font-extrabold text-lg text-ink">{{ $title }}</h2>
                <a href="{{ route('admin.'.$key.'.index') }}" class="text-sm font-bold text-slate-500 hover:text-ink">Kelola semua</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($list as $item)
                    <li class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.'.$key.'.show', $item->id) }}" class="font-bold text-ink hover:underline line-clamp-1">{{ $item->title }}</a>
                            <p class="text-xs text-slate-500">{{ $item->user->name }}, {{ $item->event_date->translatedFormat('d M Y') }}</p>
                        </div>
                        @include('admin._badge')
                    </li>
                @empty
                    <li class="px-5 py-6 text-sm text-slate-500">Belum ada laporan.</li>
                @endforelse
            </ul>
        </section>
    @endforeach
</div>
@endsection
