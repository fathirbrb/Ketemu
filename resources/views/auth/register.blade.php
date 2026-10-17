@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="max-w-md mx-auto panel p-8">
    <h1 class="font-display font-extrabold text-3xl text-ink">Buat akun Ketemu</h1>
    <p class="text-slate-500 text-sm mt-1">Gratis. Cukup satu akun untuk melapor dan memantau statusnya.</p>
    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="name" class="label">Nama lengkap</label>
            <input id="name" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name" class="input">
        </div>
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input">
        </div>
        <div>
            <label for="phone" class="label">Nomor WhatsApp <span class="font-medium text-slate-400">(opsional)</span></label>
            <input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" placeholder="08xxxxxxxxxx" class="input">
        </div>
        <div>
            <label for="password" class="label">Kata sandi <span class="font-medium text-slate-400">(minimal 8 karakter)</span></label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="input">
        </div>
        <div>
            <label for="password_confirmation" class="label">Ulangi kata sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
        </div>
        <button type="submit" class="btn-primary w-full">Buat akun</button>
    </form>
    <p class="text-center text-sm mt-6 text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-ink underline">Masuk</a></p>
</div>
@endsection
