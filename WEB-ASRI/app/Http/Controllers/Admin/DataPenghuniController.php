<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DataPenghuniController extends Controller
{
    public function index()
    {
        $penghuni = User::where('role', 'penghuni')
            ->with([
                'pengajuanSewas' => function ($q) {
                    $q->latest();
                }
            ])
            ->latest()
            ->paginate(10);

        return view('admin.penghuni.index', compact('penghuni'));
    }

    public function show($id)
    {
        $penghuni = User::with([
            'dokumens',
            'pengajuanSewas' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        return view('admin.penghuni.show', compact('penghuni'));
    }

    /**
     * Alias route admin.penghuni.view
     */
    public function viewBerkas($id, $kolom)
    {
        return $this->viewDokumen($id, $kolom);
    }

    /**
     * Preview dokumen di browser
     */
    public function viewDokumen($id, $jenis)
    {
        $this->validateKolom($jenis);

        $penghuni = User::findOrFail($id);

        $doc = $penghuni->dokumens()
            ->where('jenis_dokumen', $jenis)
            ->first();

        if (
            !$doc ||
            !$doc->path_file ||
            !Storage::disk('public')->exists($doc->path_file)
        ) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->file(
            storage_path('app/public/' . $doc->path_file)
        );
    }

    /**
     * Download dokumen
     */
    public function downloadDokumen($id, $jenis)
    {
        $this->validateKolom($jenis);

        $penghuni = User::findOrFail($id);

        $doc = $penghuni->dokumens()
            ->where('jenis_dokumen', $jenis)
            ->first();

        if (
            !$doc ||
            !$doc->path_file ||
            !Storage::disk('public')->exists($doc->path_file)
        ) {
            return back()->with('error', 'Gagal mengunduh berkas.');
        }

        $extension = pathinfo($doc->path_file, PATHINFO_EXTENSION);

        $fileName = $jenis . '-' .
            str_replace(' ', '-', strtolower($penghuni->name)) .
            '.' . $extension;

        return response()->download(
            storage_path('app/public/' . $doc->path_file),
            $fileName
        );
    }

    /**
     * Mengeluarkan penghuni
     */
    public function kick($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Kembalikan role
            $user->update([
                'role' => 'calon_penghuni',
                'is_active' => false
            ]);

            // Selesaikan kontrak aktif
            if (method_exists($user, 'kontrakSewas')) {
                $user->kontrakSewas()
                    ->where('status', 'aktif')
                    ->update([
                        'status' => 'selesai'
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.penghuni.index')
                ->with(
                    'success',
                    "Berhasil! Status {$user->name} kini kembali menjadi Calon Penghuni."
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    /**
     * Validasi jenis dokumen
     */
    private function validateKolom($kolom)
    {
        $allowedColumns = [
            'ktp',
            'kk',
            'sk_kerja',
            'foto_profil',
            'ijazah',
            'slip_gaji'
        ];

        if (!in_array($kolom, $allowedColumns)) {
            abort(404, 'Jenis dokumen tidak dikenal.');
        }
    }
}