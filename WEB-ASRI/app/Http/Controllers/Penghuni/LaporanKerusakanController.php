<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\KontrakSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Wajib di-import untuk hapus redline
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    /**
     * Menampilkan daftar laporan kerusakan milik penghuni.
     */
    public function index(Request $request): View
    {
        $query = LaporanKerusakan::with('unit')
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $laporan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $summary = [
            'open'        => LaporanKerusakan::where('user_id', Auth::id())->where('status', 'open')->count(),
            'in_progress' => LaporanKerusakan::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'resolved'    => LaporanKerusakan::where('user_id', Auth::id())->where('status', 'resolved')->count(),
        ];

        return view('penghuni.laporan.index', compact('laporan', 'summary'));
    }

    /**
     * Form buat laporan kerusakan baru.
     */
    public function create(): View|RedirectResponse
    {
        $kontrak = KontrakSewa::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->with('unit')
            ->first();

        if (!$kontrak) {
            return redirect()->route('penghuni.dashboard')
                ->with('error', 'Anda tidak memiliki unit aktif untuk dilaporkan.');
        }

        $kategoris = [
            'listrik'   => 'Kelistrikan',
            'plumbing'  => 'Pipa/Saluran Air',
            'struktur'  => 'Struktur Bangunan',
            'fasilitas' => 'Fasilitas Umum',
            'lainnya'   => 'Lainnya',
        ];

        return view('penghuni.laporan.create', compact('kontrak', 'kategoris'));
    }

    /**
     * Simpan laporan kerusakan ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kategori'  => 'required|in:listrik,plumbing,struktur,fasilitas,lainnya',
            'judul'     => 'required|string|min:5|max:100',
            'deskripsi' => 'required|string|min:20|max:1000',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        /** @var \App\Models\KontrakSewa $kontrak */
        $kontrak = KontrakSewa::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->firstOrFail();

        $data = $request->only(['kategori', 'judul', 'deskripsi']);
        $data['user_id'] = Auth::id();
        $data['unit_id'] = $kontrak->unit_id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('laporan_kerusakan', 'public');
        }

        /** @var \App\Models\LaporanKerusakan $laporan */
        $laporan = LaporanKerusakan::create($data);

        return redirect()->route('penghuni.laporan.show', $laporan->id)
            ->with('success', 'Laporan kerusakan berhasil dikirim! Teknisi akan segera menindaklanjuti.');
    }

    /**
     * Detail laporan kerusakan.
     */
    public function show(string|int $id): View
    {
        $laporan = LaporanKerusakan::with('unit')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('penghuni.laporan.show', compact('laporan'));
    }

    /**
     * Menutup tiket laporan.
     */
    public function close(string|int $id): RedirectResponse
    {
        /** @var \App\Models\LaporanKerusakan $laporan */
        $laporan = LaporanKerusakan::where('user_id', Auth::id())
            ->whereIn('status', ['open', 'resolved'])
            ->findOrFail($id);

        $laporan->update(['status' => 'closed']);

        return redirect()->route('penghuni.laporan.index')
            ->with('success', 'Tiket laporan telah ditutup. Terima kasih!');
    }
}