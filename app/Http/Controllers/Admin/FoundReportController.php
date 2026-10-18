<?php

namespace App\Http\Controllers\Admin;

use App\Models\FoundItem;

class FoundReportController extends AdminReportController
{
    protected function model(): string
    {
        return FoundItem::class;
    }

    protected function key(): string
    {
        return 'found';
    }

    protected function label(): string
    {
        return 'Barang Temuan';
    }

    protected function desc(): string
    {
        return 'Barang yang ditemukan orang lain dan menunggu pemiliknya.';
    }

    protected function dateLabel(): string
    {
        return 'Tanggal ditemukan';
    }
}
