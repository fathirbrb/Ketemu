<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;
use App\Models\LostItem;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'stats' => [
                'lost' => LostItem::count(),
                'found' => FoundItem::count(),
                'done' => LostItem::where('status', 'ditemukan')->count() + FoundItem::where('status', 'diklaim')->count(),
            ],
            'latestLost' => LostItem::with('category')->latest('id')->take(3)->get(),
            'latestFound' => FoundItem::with('category')->latest('id')->take(3)->get(),
        ]);
    }
}
