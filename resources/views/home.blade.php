@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<section class="grid md:grid-cols-5 gap-8 items-center bg-ink text-white rounded-2xl p-8 md:p-12 overflow-hidden">
    <div class="md:col-span-3">
        <h1 class="font-display font-extrabold text-4xl md:text-6xl leading-[1.05] tracking-tight">Barangmu hilang? Cari di sini sebelum terlambat.</h1>
        <p class="mt-5 text-white/75 text-lg max-w-xl">Laporkan barang yang hilang atau yang kamu temukan di kampus, sekolah, tempat wisata, dan fasilitas umum. Pemiliknya bisa langsung menghubungimu.</p>
        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('lost.create') }}" class="btn-tag">Laporkan barang hilang</a>
            <a href="{{ route('found.create') }}" class="inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-bold border border-white/30 hover:bg-white/10 transition">Laporkan barang temuan</a>
        </div>
    </div>
    <div class="md:col-span-2 flex justify-center" aria-hidden="true">
        <div class="relative">
            <div class="mx-auto w-px h-10 bg-white/40"></div>
            <div class="relative w-60 bg-tag text-ink rounded-xl rotate-3 p-5 shadow-2xl">
                <span class="absolute top-3 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-ink"></span>
                <p class="mt-5 text-xs font-bold text-ink/60">Dicari pemiliknya</p>
                <p class="font-display font-extrabold text-2xl leading-tight">Dompet kulit cokelat</p>
                <p class="mt-2 text-sm">Perpustakaan Pusat</p>
                <p class="mt-3 pt-3 border-t border-dashed border-ink/40 text-xs font-bold">{{ $stats['done'] }} barang sudah kembali ke pemiliknya</p>
            </div>
        </div>
    </div>
</section>

@foreach ([['lost', 'Barang Hilang', 'Baru dilaporkan hilang', $latestLost], ['found', 'Barang Temuan', 'Baru ditemukan orang lain', $latestFound]] as [$key, $label, $sub, $list])
    @php $meta = ['key' => $key]; @endphp
    <section class="mt-12">
        <div class="flex items-end justify-between gap-4 mb-5">
            <div>
                <h2 class="font-display font-extrabold text-3xl text-ink">{{ $label }}</h2>
                <p class="text-slate-500">{{ $sub }} · total {{ $stats[$key] }} laporan</p>
            </div>
            <a href="{{ route($key.'.index') }}" class="btn-line">Lihat semua</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($list as $item)
                @include('partials.card')
            @empty
                <div class="panel p-8 text-center text-slate-500 col-span-full">Belum ada laporan. <a class="font-bold text-ink underline" href="{{ route($key.'.create') }}">Jadilah yang pertama.</a></div>
            @endforelse
        </div>
    </section>
@endforeach

<section class="mt-14 panel p-8">
    <h2 class="font-display font-extrabold text-2xl text-ink mb-5">Cara kerja Ketemu</h2>
    <ol class="grid md:grid-cols-3 gap-6 text-sm">
        <li><p class="font-bold text-ink text-base">1. Buat laporan</p><p class="mt-1 text-slate-600">Isi nama barang, lokasi, tanggal, dan tambahkan foto agar mudah dikenali.</p></li>
        <li><p class="font-bold text-ink text-base">2. Temukan kecocokan</p><p class="mt-1 text-slate-600">Cari berdasarkan kategori atau lokasi, lalu buka laporan yang mirip dengan barangmu.</p></li>
        <li><p class="font-bold text-ink text-base">3. Hubungi dan tutup laporan</p><p class="mt-1 text-slate-600">Hubungi pelapor lewat WhatsApp, lalu ubah status laporan setelah barang kembali.</p></li>
    </ol>
</section>
@endsection
