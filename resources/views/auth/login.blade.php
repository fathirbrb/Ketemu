@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="max-w-md mx-auto panel p-8">
    <h1 class="font-display font-extrabold text-3xl text-ink">Masuk ke Ketemu</h1>
    <p class="text-slate-500 text-sm mt-1">Masuk untuk membuat dan mengelola laporanmu.</p>
    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="input">
        </div>
        <div>
            <label for="password" class="label">Kata sandi</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-ink focus:ring-ink"> Ingat saya di perangkat ini
        </label>
        <button type="submit" class="btn-primary w-full">Masuk</button>
    </form>
    <p class="text-center text-sm mt-6 text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-ink underline">Daftar</a></p>
</div>
@endsection
