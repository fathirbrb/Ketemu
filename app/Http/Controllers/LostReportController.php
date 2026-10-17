<?php

namespace App\Http\Controllers;

use App\Models\LostItem;

class LostReportController extends ReportController
{
    protected function model(): string
    {
        return LostItem::class;
    }

    protected function key(): string
    {
        return 'lost';
    }
}
