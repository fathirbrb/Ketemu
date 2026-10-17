<?php

namespace App\Models;

use App\Models\Concerns\IsReport;
use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    use IsReport;

    public const LABEL = 'Barang Hilang';
    public const DESC = 'Daftar barang yang sedang dicari pemiliknya.';
    public const DATE_LABEL = 'Tanggal hilang';
    public const STATUSES = ['hilang' => 'Masih hilang', 'ditemukan' => 'Sudah ditemukan'];

    protected $fillable = ['user_id', 'category_id', 'title', 'description', 'location', 'event_date', 'contact', 'photo', 'status'];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }
}
