@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<div class="ketemu-landing">
<section class="landing-hero">
    <div class="hero-copy"><p class="landing-eyebrow">KEHILANGAN BARANG, BUKAN HARAPAN</p><h1>Temukan kembali.<br><span>Berbagi kabar baik.</span></h1><p class="hero-description">Barang berharga punya cerita. Cari barangmu yang hilang atau bantu temuanmu kembali ke pemiliknya.</p></div>
    <form action="{{ route('lost.index') }}" method="GET" class="hero-search">
        <div><label for="home-keyword">Barang atau lokasi</label><input id="home-keyword" name="q" type="search" maxlength="150" placeholder="Contoh: dompet, perpustakaan"></div>
        <div><label for="home-status">Status laporan</label><select id="home-status" name="status"><option value="">Semua status</option><option value="hilang">Masih hilang</option><option value="ditemukan">Sudah ditemukan</option></select></div>
        <button type="submit">Cari barang hilang</button>
    </form>
    <div class="hero-notes"><span>✓ Laporan dengan foto</span><span>✓ Cari berdasarkan lokasi</span><span>✓ Hubungi pelapor langsung</span></div>
    <a href="{{ route('found.index') }}" class="hero-found-link">Menemukan barang? Lihat laporan temuan</a>
</section>
<section class="landing-section landing-how">
    <div class="landing-heading"><p class="section-kicker">CARA KERJA KETEMU</p><h2>Tiga langkah kecil.<br>Kabar baik untuk seseorang.</h2><p>Mulai dari satu laporan, sampai barang kembali ke tangan yang tepat.</p></div>
    <ol class="step-grid">
        @foreach ([['01', '⌕', 'Cari atau laporkan', 'Cari barang yang mirip dengan milikmu. Belum ada? Buat laporan dengan ciri dan lokasi yang jelas.'], ['02', '▧', 'Cocokkan detailnya', 'Periksa foto, lokasi, tanggal, dan deskripsi untuk memastikan barang yang kamu cari.'], ['03', '✓', 'Hubungi pelapor', 'Gunakan kontak di laporan, atur pengembalian, lalu perbarui status setelah selesai.']] as [$number, $icon, $title, $description])
        <li class="step-card"><span class="step-number">{{ $number }}</span><span class="step-icon" aria-hidden="true">{{ $icon }}</span><h3>{{ $title }}</h3><p>{{ $description }}</p></li>
        @endforeach
    </ol>
    <a href="{{ route('lost.create') }}" class="landing-button">Mulai buat laporan</a>
</section>
<section class="landing-stats" aria-label="Ringkasan laporan">
    <div><strong>{{ $stats['lost'] }}</strong><span>Laporan barang hilang</span></div><div><strong>{{ $stats['found'] }}</strong><span>Laporan barang temuan</span></div><div><strong>{{ $stats['done'] }}</strong><span>Laporan selesai</span></div>
</section>
@foreach ([['found', 'Kabar dari barang temuan', 'Mungkin yang kamu cari sudah ditemukan seseorang.', $latestFound]] as [$key, $label, $sub, $list])
<section class="landing-section landing-reports landing-reports-{{ $key }}">
    <div class="report-section-heading"><div><p class="section-kicker">{{ $key === 'lost' ? 'SEDANG DICARI' : 'MENUNGGU PEMILIK' }}</p><h2>{{ $label }}</h2><p>{{ $sub }}</p></div><a href="{{ route($key.'.index') }}" class="report-all">Lihat semua</a></div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($list as $item)
            @include('partials.card')
        @empty
            <div class="landing-empty col-span-full"><span aria-hidden="true">⌕</span><h3>Belum ada laporan</h3><p>Jadi yang pertama berbagi informasi di sini.</p><a href="{{ route($key.'.create') }}">Buat laporan</a></div>
        @endforelse
    </div>
</section>
@endforeach
<section class="landing-section landing-benefits">
    <div class="landing-heading"><p class="section-kicker">LEBIH MUDAH SALING MEMBANTU</p><h2>Satu tempat untuk mencari<br>dan mengembalikan.</h2></div>
    <div class="benefit-grid">
        @foreach ([['⌕', 'Pencarian terarah', 'Temukan laporan berdasarkan nama barang, lokasi, kategori, dan status.'], ['▧', 'Foto yang membantu', 'Kenali barang lewat foto dan ciri khusus yang ditulis pelapor.'], ['↗', 'Kontak langsung', 'Hubungi pelapor melalui nomor yang tercantum di detail laporan.'], ['✓', 'Status yang jelas', 'Tandai barang yang sudah kembali agar informasi tetap diperbarui.']] as [$icon, $title, $description])
        <div><h3>{{ $title }}</h3><p>{{ $description }}</p></div>
        @endforeach
    </div>
</section>
<section class="landing-cta"><div><p class="section-kicker">SEDIKIT PEDULI, BESAR ARTINYA</p><h2>Temuanmu bisa jadi<br>kabar yang ditunggu.</h2></div><div><p>Kalau menemukan barang, bantu pemiliknya dengan membuat laporan. Sertakan foto dan lokasi agar mudah dikenali.</p><a href="{{ route('found.create') }}" class="landing-button">Laporkan barang temuan</a></div>
</section>
</div>
@endsection