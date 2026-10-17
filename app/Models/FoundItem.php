<?php

namespace App\Models;

use App\Models\Concerns\IsReport;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use IsReport;

    public const LABEL = 'Barang Temuan';
    public const DESC = 'Barang yang ditemukan orang lain dan menunggu pemiliknya.';
    public const DATE_LABEL = 'Tanggal ditemukan';
    public const STATUSES = ['tersimpan' => 'Menunggu pemilik', 'diklaim' => 'Sudah diklaim'];

    protected $fillable = ['user_id', 'category_id', 'title', 'description', 'location', 'event_date', 'contact', 'photo', 'status'];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }
}
