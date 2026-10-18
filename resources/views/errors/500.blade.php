@extends('layouts.app')
@section('title', 'Terjadi gangguan')
@section('content')
<div class="panel p-10 text-center max-w-xl mx-auto">
    <p class="font-display font-extrabold text-6xl text-tag">500</p>
    <h1 class="mt-3 font-display font-extrabold text-2xl text-ink">Terjadi gangguan</h1>
    <p class="mt-2 text-slate-500">Ada masalah di server. Coba lagi beberapa saat lagi.</p>
    <a href="{{ route('home') }}" class="btn-primary mt-6">Kembali ke beranda</a>
</div>
@endsection
