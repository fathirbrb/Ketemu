@extends('layouts.app')
@section('title', 'Sesi habis')
@section('content')
<div class="panel p-10 text-center max-w-xl mx-auto">
    <p class="font-display font-extrabold text-6xl text-tag">419</p>
    <h1 class="mt-3 font-display font-extrabold text-2xl text-ink">Sesi habis</h1>
    <p class="mt-2 text-slate-500">Halaman sudah kedaluwarsa. Muat ulang lalu coba lagi.</p>
    <a href="{{ route('home') }}" class="btn-primary mt-6">Kembali ke beranda</a>
</div>
@endsection
