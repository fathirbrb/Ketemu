<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LostDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Data contoh barang hilang hanya untuk lingkungan local/testing.');
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
                ['email' => 'demo.barang.hilang@ketemu.test'],
                ['name' => 'Pelapor Demo', 'password' => Str::random(40)]
            );

            foreach ([
                ['Dompet kulit cokelat (contoh)', 'Dompet & Tas', 'Perpustakaan Pusat', 'Dompet cokelat berukuran kecil. Ini data contoh untuk pengujian, bukan laporan kehilangan nyata.', 'hilang', 1],
                ['iPhone casing hijau (contoh)', 'Elektronik', 'Kantin Fakultas', 'Ponsel dengan casing hijau tua. Ini data contoh untuk menguji pencarian dan filter.', 'hilang', 2],
                ['Kunci motor (contoh)', 'Kunci', 'Parkiran Gedung A', 'Kunci dengan gantungan merah. Contoh laporan yang sudah selesai.', 'ditemukan', 3],
            ] as [$title, $category, $location, $description, $status, $days]) {
                LostItem::firstOrCreate(
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