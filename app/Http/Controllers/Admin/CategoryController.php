<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount(['lostItems', 'foundItems'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:8'],
        ]);

        try {
            Category::create($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Kategori gagal disimpan. Coba lagi.');
        }

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('categories', 'name')->ignore($category->id)],
            'icon' => ['nullable', 'string', 'max:8'],
        ]);

        try {
            $category->update($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Kategori gagal diperbarui. Coba lagi.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->lostItems()->exists() || $category->foundItems()->exists()) {
            return back()->with('error', 'Kategori "'.$category->name.'" masih dipakai laporan, jadi belum bisa dihapus.');
        }

        try {
            $category->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Kategori gagal dihapus. Coba lagi.');
        }

        return back()->with('success', 'Kategori dihapus.');
    }
}
