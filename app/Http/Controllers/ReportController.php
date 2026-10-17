<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Basis CRUD laporan. Dipakai oleh LostReportController (barang hilang)
 * dan FoundReportController (barang temuan).
 */
abstract class ReportController extends Controller
{
    abstract protected function model(): string;

    abstract protected function key(): string; // 'lost' | 'found'

    protected function meta(): array
    {
        $m = $this->model();

        return [
            'key' => $this->key(),
            'title' => $m::LABEL,
            'desc' => $m::DESC,
            'dateLabel' => $m::DATE_LABEL,
            'statuses' => $m::STATUSES,
        ];
    }

    protected function rules(): array
    {
        $m = $this->model();

        return [
            'title' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'location' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'contact' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,30}$/'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['nullable', Rule::in(array_keys($m::STATUSES))],
        ];
    }

    protected function authorizeManage($item): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $item->user_id !== $user->id) {
            abort(403);
        }
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
            ->when($request->boolean('mine') && Auth::check(), fn ($q) => $q->where('user_id', Auth::id()))
            ->latest('event_date')->latest('id')
            ->paginate(9)->withQueryString();

        return view('reports.index', [
            'meta' => $this->meta(),
            'items' => $items,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(int $id)
    {
        $item = $this->model()::with(['category', 'user'])->findOrFail($id);

        return view('reports.show', ['meta' => $this->meta(), 'item' => $item]);
    }

    public function create()
    {
        return view('reports.form', [
            'meta' => $this->meta(),
            'item' => null,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        unset($data['photo']);

        try {
            $data['user_id'] = Auth::id();
            $data['status'] = $data['status'] ?? array_key_first($this->model()::STATUSES);
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store($this->key(), 'public');
            }
            $this->model()::create($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Laporan belum tersimpan karena ada gangguan. Coba kirim ulang.');
        }

        return redirect()->route($this->key().'.index', ['mine' => 1])->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = $this->model()::findOrFail($id);
        $this->authorizeManage($item);

        return view('reports.form', [
            'meta' => $this->meta(),
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $item = $this->model()::findOrFail($id);
        $this->authorizeManage($item);

        $data = $request->validate($this->rules());
        unset($data['photo']);

        try {
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store($this->key(), 'public');
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

        return redirect()->route($this->key().'.show', $item->id)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = $this->model()::findOrFail($id);
        $this->authorizeManage($item);

        try {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }
            $item->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Laporan gagal dihapus. Coba lagi.');
        }

        return redirect()->route($this->key().'.index', ['mine' => 1])->with('success', 'Laporan berhasil dihapus.');
    }
}
