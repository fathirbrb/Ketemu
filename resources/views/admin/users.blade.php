@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('content')
<h1 class="font-display font-extrabold text-4xl text-ink mb-6">Pengguna</h1>
@include('admin._nav')

<div class="panel overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-slate-500 border-b border-slate-200">
            <tr><th class="px-4 py-3 font-bold">Nama</th><th class="px-4 py-3 font-bold">Email</th><th class="px-4 py-3 font-bold">Peran</th><th class="px-4 py-3 font-bold">Laporan</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($users as $u)
                <tr>
                    <td class="px-4 py-3 font-bold text-ink">{{ $u->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                    <td class="px-4 py-3"><span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $u->isAdmin() ? 'bg-ink text-white' : 'bg-slate-100 text-slate-700' }}">{{ $u->isAdmin() ? 'Admin' : 'Pengguna' }}</span></td>
                    <td class="px-4 py-3 text-slate-600">{{ $u->lost_items_count }} hilang, {{ $u->found_items_count }} temuan</td>
                    <td class="px-4 py-3 text-right">
                        @unless ($u->isAdmin())
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Hapus pengguna ini beserta semua laporannya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5">Hapus</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
