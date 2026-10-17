<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::where('role', 'user')->count(),
                'categories' => Category::count(),
                'lost' => LostItem::count(),
                'found' => FoundItem::count(),
            ],
            'latestLost' => LostItem::with(['user', 'category'])->latest('id')->take(5)->get(),
            'latestFound' => FoundItem::with(['user', 'category'])->latest('id')->take(5)->get(),
        ]);
    }
}
