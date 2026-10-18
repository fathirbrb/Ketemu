<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Kelola laporan dari panel admin. Berdiri sendiri (tidak mengubah controller
 * Dila/Caca) dan hanya memakai model LostItem/FoundItem yang sudah ada.
 */
abstract class AdminReportController extends Controller
{
    abstract protected function model(): string;

    abstract protected function key(): string; // 'lost' | 'found'

    abstract protected function label(): string;

    abstract protected function desc(): string;

    abstract protected function dateLabel(): string;

    protected function meta(): array
    {
        $m = $this->model();

        return [
            'key' => $this->key(),
            'route' => 'admin.'.$this->key(),
            'title' => $this->label(),
            'desc' => $this->desc(),
            'dateLabel' => $this->dateLabel(),
            'statuses' => $m::STATUSES,
        ];
    }

    protected function rules(): array
    {
        $m = $this->model();

        return [
            'title' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'contact' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['nullable', Rule::in(array_keys($m::STATUSES))],
        ];
    }

    protected function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute melebihi batas yang diizinkan.',
            'event_date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia.',
            'contact.regex' => 'Kontak harus berisi 8 sampai 15 angka, boleh diawali tanda +.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Foto harus berupa JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'status.in' => 'Status laporan tidak valid.',
        ];
    }

    protected function attributes(): array
    {
        return [
            'title' => 'Nama barang', 'category_id' => 'Kategori', 'description' => 'Keterangan',
            'location' => 'Lokasi', 'event_date' => 'Tanggal', 'contact' => 'Kontak',
            'photo' => 'Foto', 'status' => 'Status',
        ];
    }

    /** Simpan foto lewat getPathname() agar aman di Windows. */
    protected function storePhoto(UploadedFile $file): string
    {
        $path = $this->key().'/'.$file->hashName();
        $contents = file_get_contents($file->getPathname());

        if ($contents === false || ! Storage::disk('public')->put($path, $contents)) {
            throw new \RuntimeException('Foto gagal disimpan ke storage.');
        }

        return $path;
    }

    public function index(Request $request)
    {
        $m = $this->model();
        $term = $request->filled('q')
            ? '%'.str_replace(['%', '_'], ['\%', '\_'], $request->string('q')->toString()).'%'
            : null;

        $items = $m::with(['category', 'user'])
            ->when($term, fn ($q) => $q->where(fn ($w) => $w->where('title', 'ilike', $term)->orWhere('location', 'ilike', $term)))
            ->when($request->integer('category') > 0, fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest('event_date')->latest('id')
            ->paginate(10)->withQueryString();

        return view('admin.reports.index', [
            'meta' => $this->meta(),
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(int $id)
    {
        $item = $this->model()::with(['category', 'user'])->findOrFail($id);

        return view('admin.reports.show', ['meta' => $this->meta(), 'item' => $item]);
    }

    public function create()
    {
        return view('admin.reports.form', [
            'meta' => $this->meta(),
            'item' => null,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages(), $this->attributes());
        unset($data['photo']);

        try {
            $data['user_id'] = Auth::id();
            $data['status'] = $data['status'] ?? array_key_first($this->model()::STATUSES);
            if ($request->hasFile('photo')) {
                $data['photo'] = $this->storePhoto($request->file('photo'));
            }
            $this->model()::create($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Laporan belum tersimpan karena ada gangguan. Coba kirim ulang.');
        }

        return redirect()->route('admin.'.$this->key().'.index')->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return view('admin.reports.form', [
            'meta' => $this->meta(),
            'item' => $this->model()::findOrFail($id),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $item = $this->model()::findOrFail($id);
        $data = $request->validate($this->rules(), $this->messages(), $this->attributes());
        unset($data['photo']);

        try {
            if ($request->hasFile('photo')) {
                $data['photo'] = $this->storePhoto($request->file('photo'));
                if ($item->photo) {
                    Storage::disk('public')->delete($item->photo);
                }
            } elseif ($request->boolean('remove_photo') && $item->photo) {
                Storage::disk('public')->delete($item->photo);
                $data['photo'] = null;
            }
            $item->update($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Perubahan belum tersimpan karena ada gangguan. Coba kirim ulang.');
        }

        return redirect()->route('admin.'.$this->key().'.show', $item->id)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = $this->model()::findOrFail($id);

        try {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }
            $item->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Laporan gagal dihapus. Coba lagi.');
        }

        return redirect()->route('admin.'.$this->key().'.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
