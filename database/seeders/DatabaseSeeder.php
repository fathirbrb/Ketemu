<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Admin Ketemu', 'email' => 'admin@ketemu.test', 'password' => 'password', 'role' => 'admin', 'phone' => '081234567890']);
        $user = User::create(['name' => 'Budi Santoso', 'email' => 'user@ketemu.test', 'password' => 'password', 'role' => 'user', 'phone' => '081298765432']);

        $cats = [];
        foreach ([
            'Dompet & Tas' => '👜', 'Elektronik' => '📱', 'Dokumen & Kartu' => '🪪',
            'Kunci' => '🔑', 'Pakaian & Aksesori' => '🧥', 'Buku & Alat Tulis' => '📚', 'Lainnya' => '📦',
        ] as $name => $icon) {
            $cats[$name] = Category::create(['name' => $name, 'icon' => $icon])->id;
        }

        $base = ['user_id' => $user->id, 'contact' => '081298765432'];
        foreach ([
            ['Dompet kulit cokelat', '', 'Dompet & Tas', 'Perpustakaan Pusat', 'Berisi KTM dan beberapa kartu ATM. Hilang setelah jam makan siang.'],
            ['iPhone casing hijau', '', 'Elektronik', 'Kantin Fakultas', 'HP dengan casing hijau tua dan stiker bunga di bagian belakang.'],
            ['Kunci motor Honda', '', 'Kunci', 'Parkiran Gedung A', 'Gantungan kunci berbentuk beruang kecil warna merah.'],
        ] as $i => [$title, , $cat, $loc, $desc]) {
            LostItem::create($base + ['title' => $title, 'category_id' => $cats[$cat], 'location' => $loc, 'description' => $desc, 'event_date' => now()->subDays($i + 1)->toDateString(), 'status' => 'hilang']);
        }
        foreach ([
            ['Kartu Tanda Mahasiswa', 'Dokumen & Kartu', 'Musala Kampus', 'Ditemukan di dekat tempat wudu. Disimpan di pos satpam.'],
            ['Payung lipat biru', 'Lainnya', 'Ruang Kuliah B2', 'Tertinggal di bawah kursi baris ketiga.'],
            ['Buku catatan bersampul hitam', 'Buku & Alat Tulis', 'Taman Fakultas', 'Ada nama inisial "R.A." di halaman pertama.'],
        ] as $i => [$title, $cat, $loc, $desc]) {
            FoundItem::create($base + ['title' => $title, 'category_id' => $cats[$cat], 'location' => $loc, 'description' => $desc, 'event_date' => now()->subDays($i)->toDateString(), 'status' => 'tersimpan']);
        }
    }
}
