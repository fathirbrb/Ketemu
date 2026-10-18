<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoundItem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FoundReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', Rule::in(array_keys(FoundItem::STATUSES))],
            'mine' => ['nullable', 'boolean'],
        ]);

        $query = FoundItem::with('category')->latest('id');

        if ($keyword = trim($filters['q'] ?? '')) {
            $query->where(function ($query) use ($keyword) {
                $query->whereLike('title', '%'.$keyword.'%')
                    ->orWhereLike('location', '%'.$keyword.'%');
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ($request->boolean('mine')) {
            abort_unless($request->user(), 403);

            $query->where('user_id', $request->user()->id);
        }

        return view('found.index', [
            'items' => $query->paginate(9)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(FoundItem $foundItem)
    {
        $foundItem->load(['category', 'user']);

        return view('found.show', ['item' => $foundItem]);
    }

    public function create()
    {
        return view('found.form', [
            'item' => new FoundItem(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $photo = null;

        try {
            if ($request->hasFile('photo')) {
                $photo = $this->storePhoto($request->file('photo'));
            }

            $item = FoundItem::create([
                ...$data,
                'user_id' => $request->user()->id,
                'photo' => $photo,
                'status' => 'tersimpan',
            ]);
        } catch (\Throwable $exception) {
            if ($photo) {
                $this->deletePhoto($photo);
            }

            report($exception);

            return back()->withInput($request->except('photo'))
                ->with('error', 'Laporan gagal disimpan. Coba lagi.');
        }

        return redirect()->route('found.show', $item)
            ->with('success', 'Laporan barang temuan berhasil dibuat.');
    }

    public function edit(Request $request, FoundItem $foundItem)
    {
        $this->checkOwner($request, $foundItem);

        return view('found.form', [
            'item' => $foundItem,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, FoundItem $foundItem)
    {
        $this->checkOwner($request, $foundItem);

        $data = $this->validatedData($request, true);
        $oldPhoto = $foundItem->photo;
        $newPhoto = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhoto = $this->storePhoto($request->file('photo'));
                $data['photo'] = $newPhoto;
            } elseif ($request->boolean('remove_photo')) {
                $data['photo'] = null;
            }

            $foundItem->update($data);
        } catch (\Throwable $exception) {
            if ($newPhoto) {
                $this->deletePhoto($newPhoto);
            }

            report($exception);

            return back()->withInput($request->except('photo'))
                ->with('error', 'Laporan gagal diperbarui. Coba lagi.');
        }

        if ($oldPhoto && $oldPhoto !== $foundItem->photo) {
            $this->deletePhoto($oldPhoto);
        }

        return redirect()->route('found.show', $foundItem)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, FoundItem $foundItem)
    {
        $this->checkOwner($request, $foundItem);

        $photo = $foundItem->photo;

        try {
            $foundItem->delete();
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Laporan gagal dihapus. Coba lagi.');
        }

        if ($photo) {
            $this->deletePhoto($photo);
        }

        return redirect()->route('found.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    private function checkOwner(Request $request, FoundItem $item): void
    {
        abort_unless(
            $request->user()
                && (int) $request->user()->id === (int) $item->user_id,
            403
        );
    }

    private function validatedData(Request $request, bool $editing = false): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'contact' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
        ];

        if ($editing) {
            $rules['status'] = [
                'required',
                Rule::in(array_keys(FoundItem::STATUSES)),
            ];
        }

        $data = $request->validate($rules, [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute melebihi batas yang diizinkan.',
            'event_date.before_or_equal' => 'Tanggal ditemukan tidak boleh di masa depan.',
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia.',
            'contact.regex' => 'Kontak harus berisi 8–15 angka dengan awalan + opsional.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Foto harus berupa JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'status.in' => 'Status laporan tidak valid.',
        ], [
            'title' => 'Judul',
            'category_id' => 'Kategori',
            'description' => 'Deskripsi',
            'location' => 'Lokasi',
            'event_date' => 'Tanggal ditemukan',
            'contact' => 'Kontak',
            'photo' => 'Foto',
            'status' => 'Status',
        ]);

        unset($data['photo'], $data['remove_photo']);

        return $data;
    }

    /** Simpan foto lewat getPathname() supaya aman di Windows/Laragon. */
    private function storePhoto(UploadedFile $file): string
    {
        $path = 'found/'.$file->hashName();
        $contents = file_get_contents($file->getPathname());

        if ($contents === false || ! Storage::disk('public')->put($path, $contents)) {
            throw new \RuntimeException('Foto gagal disimpan ke storage.');
        }

        return $path;
    }

    private function deletePhoto(string $path): void
    {
        try {
            Storage::disk('public')->delete($path);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}