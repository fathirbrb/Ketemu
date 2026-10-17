<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users', [
            'users' => User::withCount(['lostItems', 'foundItems'])->orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }
        if ($user->isAdmin()) {
            return back()->with('error', 'Akun admin tidak bisa dihapus.');
        }

        try {
            foreach ($user->lostItems as $i) {
                if ($i->photo) Storage::disk('public')->delete($i->photo);
            }
            foreach ($user->foundItems as $i) {
                if ($i->photo) Storage::disk('public')->delete($i->photo);
            }
            $user->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Pengguna gagal dihapus. Coba lagi.');
        }

        return back()->with('success', 'Pengguna dan laporannya dihapus.');
    }
}
