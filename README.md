# Ketemu – Sistem Informasi Barang Hilang dan Temuan

Laravel 12 + PostgreSQL. Dibuat untuk UTP Pemrograman Web Lanjut.

## Cara pasang

```bash
composer create-project laravel/laravel ketemu
cd ketemu
# salin SEMUA isi folder ini ke dalam proyek ketemu (timpa file yang sama)
cp .env.example .env            # lalu isi DB_PASSWORD
php artisan key:generate
# buat database "ketemu" di PostgreSQL (psql -U postgres -c "CREATE DATABASE ketemu;")
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Syarat: PHP 8.2+ dengan ekstensi `pdo_pgsql`, Composer, PostgreSQL.

## Akun demo (dari seeder)

| Role     | Email              | Kata sandi |
|----------|--------------------|------------|
| Admin    | admin@ketemu.test  | password   |
| Pengguna | user@ketemu.test   | password   |

## Push ke GitHub

```bash
git init
git add .
git commit -m "Ketemu: sistem barang hilang dan temuan"
git branch -M main
git remote add origin https://github.com/USERNAME/ketemu.git
git push -u origin main
```

## Pemenuhan ketentuan UTP

- 2 role dengan CRUD: Admin (kategori, pengguna, dan semua laporan), Pengguna (laporan miliknya).
- 2 CRUD selain login/register: laporan barang hilang dan laporan barang temuan.
- Error handling: validasi berbahasa Indonesia, try/catch di setiap simpan/ubah/hapus, halaman 401/403/404/419/429/500/503 buatan sendiri.
  Pastikan `APP_DEBUG=false` di `.env` saat dikumpulkan.
- UI: Tailwind (CDN), responsif, kartu bergaya tag barang.
