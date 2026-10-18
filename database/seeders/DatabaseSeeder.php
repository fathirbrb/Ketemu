<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (hanya untuk pengembangan lokal). Password: "password"
        User::updateOrCreate(
            ['email' => 'admin@ketemu.test'],
            ['name' => 'Admin Ketemu', 'password' => 'password', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'user@ketemu.test'],
            ['name' => 'User Demo', 'password' => 'password', 'role' => 'user']
        );

        // Kategori dasar, supaya form laporan langsung bisa dipakai.
        foreach ([
            'Dompet & Tas' => '👜',
            'Elektronik' => '📱',
            'Kunci' => '🔑',
            'Dokumen & Kartu' => '🪪',
            'Pakaian & Aksesori' => '🧥',
            'Buku & Alat Tulis' => '📚',
            'Lainnya' => '📦',
        ] as $name => $icon) {
            Category::firstOrCreate(['name' => $name], ['icon' => $icon]);
        }

        // Data contoh laporan (hanya jalan di local/testing).
        $this->call([
            LostDemoSeeder::class,
            FoundDemoSeeder::class,
        ]);
    }
}
