<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;

class FoundReportController extends ReportController
{
    protected function model(): string
    {
        return FoundItem::class;
    }

    protected function key(): string
    {
        return 'found';
    }
}
