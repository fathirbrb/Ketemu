# Bagian 2 — Barang Hilang

Branch: `dila-barang-hilang`.

## Isi perubahan

- Model Category dan LostItem, migration categories dan lost_items.
- CRUD, filter judul/lokasi, kategori, status, dan laporan sendiri.
- Validasi tanggal, kontak, kategori, status, serta foto JPG/PNG/WebP maksimal 2 MB.
- Upload, ganti, hapus foto; hapus foto ketika laporan dihapus.
- Hanya pemilik yang dapat mengubah/menghapus laporan. Pengunjung dapat membaca.
- Route di routes/lost.php, dimuat dari routes/web.php.
- Tampilan di resources/views/lost dan layout khusus layouts/lost.blade.php.
- LostDemoSeeder opsional dan aman dijalankan ulang, hanya lingkungan local/testing.

## Integrasi anggota lain

Fathir: route bernama login harus tersedia agar middleware auth bisa mengarahkan pengunjung ke form masuk. Bagian ini tidak mengubah AuthController, User, route autentikasi, atau role admin. Setelah layout bersama lengkap, layout lost dapat digabungkan dengan layout app secara terkoordinasi.

Caca/Khalisha: gunakan tabel categories dan model Category yang sudah dibuat di bagian ini. Jangan membuat migration create_categories_table kedua. Kolom kategori: id, name (unik, maksimal 100 karakter), icon (opsional, maksimal 20 karakter), timestamps. Relasi foundItems bisa ditambahkan Caca setelah model FoundItem tersedia. CRUD kategori tetap bagian admin.

Kategori yang sedang dipakai laporan tidak dapat dihapus karena foreign key restrictOnDelete. Penghapusan user memakai cascade untuk laporan; pembersihan file foto saat admin menghapus user perlu ditangani pada bagian admin.

HomeController mengharapkan model FoundItem dari bagian 3. LostItem sudah menyediakan relasi category, casts event_date, status hilang/ditemukan dan atribut photo_url. Jika view beranda awal memakai partial kartu, partial tersebut perlu disiapkan pemilik beranda saat integrasi.

## Menjalankan

Sesuaikan .env lokal dan koneksi PostgreSQL, lalu:

```sh
php artisan migrate
php artisan storage:link
php artisan db:seed --class=LostDemoSeeder
php artisan serve
```

Seeder contoh tidak wajib dijalankan dan tidak mengubah DatabaseSeeder anggota lain. Nomor kontak pada laporan contoh adalah dummy. Akun pelapor contoh memakai password acak dan bukan akun login demo.

## Pengujian

```sh
php artisan test --filter=LostReportTest
```

Tes memakai SQLite in-memory sesuai phpunit.xml, tidak menghapus database PostgreSQL lokal. Mencakup halaman publik, pencarian/filter, CRUD pemilik, foto, validasi, serta penolakan akses user lain dan pengunjung.

Tes form dan simpan lewat browser setelah integrasi login masih perlu dilakukan sebelum merge akhir. Jangan melepas middleware auth untuk mengakali dependensi login yang belum tersedia.