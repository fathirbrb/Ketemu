<?php

namespace App\Http\Controllers\Admin;

use App\Models\LostItem;

class LostReportController extends AdminReportController
{
    protected function model(): string
    {
        return LostItem::class;
    }

    protected function key(): string
    {
        return 'lost';
    }

    protected function label(): string
    {
        return 'Barang Hilang';
    }

    protected function desc(): string
    {
        return 'Daftar barang yang sedang dicari pemiliknya.';
    }

    protected function dateLabel(): string
    {
        return 'Tanggal hilang';
    }
}
