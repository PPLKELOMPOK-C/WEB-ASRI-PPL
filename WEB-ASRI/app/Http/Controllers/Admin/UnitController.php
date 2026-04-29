<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->wilayah);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('blok', 'like', '%'.$request->search.'%')
                  ->orWhere('no_kamar', 'like', '%'.$request->search.'%')
                  ->orWhere('gedung', 'like', '%'.$request->search.'%');
            });
        }

        $units = $query->orderBy('blok')->orderBy('lantai')->orderBy('no_kamar')->paginate(15);

        return view('admin.unit.index', compact('units'));
    }

    public function create()
    {
        return view('admin.unit.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'blok'       => 'required|string|max:10',
            'lantai'     => 'required|integer|min:1|max:20',
            'no_kamar'   => 'required|string|max:10',
            'gedung'     => 'required|string|max:100',
            'alamat'     => 'required|string',
            'deskripsi'  => 'nullable|string',
            'harga_sewa' => 'required|numeric|min:0',
            'status'     => 'required|in:tersedia,dihuni,maintenance',
            'wilayah'    => 'required|in:Jakarta Pusat,Jakarta Utara,Jakarta Timur,Jakarta Selatan,Jakarta Barat',
            'luas_m2'    => 'nullable|integer|min:1',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('units', 'public');
        }

        Unit::create($validated);

        return redirect()->route('admin.unit.index')
            ->with('success', 'Unit berhasil ditambahkan!');
    }

    public function show(Unit $unit)
    {
        $unit->load(['kontrakSewas.user', 'laporanKerusakans', 'tagihans']);
        return view('admin.unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('admin.unit.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'blok'       => 'required|string|max:10',
            'lantai'     => 'required|integer|min:1|max:20',
            'no_kamar'   => 'required|string|max:10',
            'gedung'     => 'required|string|max:100',
            'alamat'     => 'required|string',
            'deskripsi'  => 'nullable|string',
            'harga_sewa' => 'required|numeric|min:0',
            'status'     => 'required|in:tersedia,dihuni,maintenance',
            'wilayah'    => 'required|in:Jakarta Pusat,Jakarta Utara,Jakarta Timur,Jakarta Selatan,Jakarta Barat',
            'luas_m2'    => 'nullable|integer|min:1',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($unit->gambar) Storage::disk('public')->delete($unit->gambar);
            $validated['gambar'] = $request->file('gambar')->store('units', 'public');
        }

        $unit->update($validated);

        return redirect()->route('admin.unit.index')
            ->with('success', 'Unit berhasil diperbarui!');
    }

    public function destroy(Unit $unit)
    {
        // Cek apakah unit masih dihuni / ada kontrak aktif
        if ($unit->kontrakSewas()->where('status', 'aktif')->exists()) {
            return back()->with('error', 'Unit tidak bisa dihapus karena masih dihuni.');
        }

        if ($unit->gambar) Storage::disk('public')->delete($unit->gambar);
        $unit->delete();

        return redirect()->route('admin.unit.index')
            ->with('success', 'Unit berhasil dihapus!');
    }
}
