<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Ini kunci untuk hapus redline pada Auth
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Menampilkan daftar berita.
     */
    public function index(Request $request): View
    {
        $query = News::with('user');

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('admin.berita.index', compact('news'));
    }

    /**
     * Form tambah berita.
     */
    public function create(): View
    {
        return view('admin.berita.create');
    }

    /**
     * Simpan berita baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'judul'        => 'required|string|min:5|max:200',
            'konten'       => 'required|string|min:20',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'is_published' => 'nullable|boolean',
        ]);

        $data = [
            'user_id'      => Auth::id(), // Mengganti auth()->id() untuk hapus redline
            'judul'        => $request->judul,
            'slug'         => Str::slug($request->judul) . '-' . time(),
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('gambar_cover')) {
            $data['gambar_cover'] = $request->file('gambar_cover')->store('news', 'public');
        }

        /** @var \App\Models\News $news */
        $news = News::create($data);

        return redirect()->route('admin.berita.show', $news->id)
            ->with('success', 'Berita berhasil disimpan.');
    }

    /**
     * Detail berita.
     */
    public function show(string|int $id): View
    {
        $news = News::with('user')->findOrFail($id);
        return view('admin.berita.show', compact('news'));
    }

    /**
     * Form edit berita.
     */
    public function edit(string|int $id): View
    {
        $news = News::findOrFail($id);
        return view('admin.berita.edit', compact('news'));
    }

    /**
     * Update berita.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        /** @var \App\Models\News $news */
        $news = News::findOrFail($id);

        $request->validate([
            'judul'        => 'required|string|min:5|max:200',
            'konten'       => 'required|string|min:20',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'is_published' => 'nullable|boolean',
        ]);

        $data = [
            'judul'        => $request->judul,
            'slug'         => Str::slug($request->judul) . '-' . $news->id,
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($data['is_published'] && !$news->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('gambar_cover')) {
            if ($news->gambar_cover) {
                Storage::disk('public')->delete($news->gambar_cover);
            }
            $data['gambar_cover'] = $request->file('gambar_cover')->store('news', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.berita.show', $news->id)
            ->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Hapus berita.
     */
    public function destroy(string|int $id): RedirectResponse
    {
        /** @var \App\Models\News $news */
        $news = News::findOrFail($id);

        if ($news->gambar_cover) {
            Storage::disk('public')->delete($news->gambar_cover);
        }

        $news->delete();

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    /**
     * Toggle status publikasi.
     */
    public function togglePublish(string|int $id): RedirectResponse
    {
        /** @var \App\Models\News $news */
        $news = News::findOrFail($id);

        $news->update([
            'is_published' => !$news->is_published,
            'published_at' => !$news->is_published ? now() : $news->published_at,
        ]);

        return back()->with('success', "Status publikasi berhasil diubah.");
    }
}