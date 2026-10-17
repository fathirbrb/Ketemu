<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FoundItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoundDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Data contoh barang temuan hanya untuk lingkungan local/testing.');
            return;
        }

        DB::transaction(function () {
            $categories = [];
            foreach ([
                'Dompet & Tas' => '👜',
                'Elektronik' => '📱',
                'Kunci' => '🔑',
                'Dokumen & Kartu' => '🪪',
                'Pakaian & Aksesori' => '🧥',
                'Buku & Alat Tulis' => '📚',
                'Lainnya' => '📦',
            ] as $name => $icon) {
                $categories[$name] = Category::firstOrCreate(
                    ['name' => $name],
                    ['icon' => $icon]
                );
            }

            // Akun khusus data contoh; tidak mengubah akun pengguna yang sudah ada.
            $user = User::firstOrCreate(
                ['email' => 'demo.barang.temuan@ketemu.test'],
                ['name' => 'Penemu Demo', 'password' => Str::random(40)]
            );

            foreach ([
                ['Dompet kulit hitam (contoh)', 'Dompet & Tas', 'Kantin Fakultas', 'Dompet hitam ditemukan di bawah meja. Ini data contoh untuk pengujian, bukan laporan nyata.', 'tersimpan', 1],
                ['Kunci motor gantungan biru (contoh)', 'Kunci', 'Parkiran Gedung B', 'Kunci motor dengan gantungan biru. Ini data contoh untuk menguji pencarian dan filter.', 'tersimpan', 2],
                ['Kartu mahasiswa (contoh)', 'Dokumen & Kartu', 'Perpustakaan Pusat', 'Kartu mahasiswa yang sudah diambil pemiliknya. Contoh laporan yang sudah selesai.', 'diklaim', 3],
            ] as [$title, $category, $location, $description, $status, $days]) {
                FoundItem::firstOrCreate(
                    ['user_id' => $user->id, 'title' => $title],
                    [
                        'category_id' => $categories[$category]->id,
                        'location' => $location,
                        'description' => $description,
                        'event_date' => now()->subDays($days)->toDateString(),
                        'contact' => '080000000000',
                        'status' => $status,
                    ]
                );
            }
        });
    }
}