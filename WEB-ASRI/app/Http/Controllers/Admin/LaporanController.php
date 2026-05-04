<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanKerusakan::with(['user', 'unit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $summary = [
            'open'        => LaporanKerusakan::where('status', 'open')->count(),
            'in_progress' => LaporanKerusakan::where('status', 'in_progress')->count(),
            'resolved'    => LaporanKerusakan::where('status', 'resolved')->count(),
            'closed'      => LaporanKerusakan::where('status', 'closed')->count(),
        ];

        return view('admin.laporan.index', compact('laporans', 'summary'));
    }

    public function show($id)
    {
        $laporan = LaporanKerusakan::with(['user', 'unit'])->findOrFail($id);
        return view('admin.laporan.show', compact('laporan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:open,in_progress,resolved,closed',
            'respon_admin'  => 'nullable|string|max:500',
        ]);

        $laporan = LaporanKerusakan::with('user')->findOrFail($id);

        $updateData = [
            'status'       => $request->status,
            'respon_admin' => $request->respon_admin,
        ];

        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $laporan->update($updateData);

        // Kirim notifikasi ke penghuni
        $statusLabel = match($request->status) {
            'in_progress' => 'sedang ditangani oleh teknisi',
            'resolved'    => 'telah diselesaikan ✅',
            'closed'      => 'ditutup',
            default       => 'diperbarui',
        };

        Notifikasi::create([
            'user_id' => $laporan->user_id,
            'judul'   => 'Update Laporan Kerusakan',
            'pesan'   => "Laporan \"{$laporan->judul}\" Anda {$statusLabel}." . ($request->respon_admin ? " Catatan teknisi: {$request->respon_admin}" : ''),
            'tipe'    => $request->status === 'resolved' ? 'success' : 'info',
            'link'    => route('penghuni.laporan.show', $laporan->id),
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui dan penghuni telah dinotifikasi.');
    }
}
