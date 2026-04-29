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
            ->with(['pengajuanSewas' => function($q) {
                $q->latest(); 
            }])
            ->latest()
            ->paginate(10);

        return view('admin.penghuni.index', compact('penghuni'));
    }

    public function show($id)
    {
        $penghuni = User::with(['dokumens', 'pengajuanSewas' => function($q) {
            $q->latest();
        }])->findOrFail($id);
        
        return view('admin.penghuni.show', compact('penghuni'));
    }

    public function downloadBerkas($id, $kolom)
    {
        // Memanggil fungsi validasi agar tidak error
        $this->validateKolom($kolom);

        $penghuni = User::findOrFail($id);
        $doc = $penghuni->dokumens()->where('jenis_dokumen', $kolom)->first();

        if (!$doc || !$doc->path_file) {
            return back()->with('error', 'Data berkas tidak tersedia.');
        }

        $fullPath = storage_path('app/public/' . $doc->path_file);
        
        if (!file_exists($fullPath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $fileName = $kolom . '-' . str_replace(' ', '-', strtolower($penghuni->name)) . '.' . $extension;
        
        return response()->download($fullPath, $fileName);
    }

    /**
     * Menangani pemutusan status penghuni (Kick)
     */
    public function kick($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // 1. Kembalikan role ke calon_penghuni
            $user->update([
                'role' => 'calon_penghuni'
            ]);

            // 2. Update Kontrak Sewa ke 'selesai'
            $user->kontrakSewas()
                 ->where('status', 'aktif')
                 ->update(['status' => 'selesai']); 

            DB::commit();
            return redirect()->route('admin.penghuni.index')
                ->with('success', "Berhasil! Status {$user->name} kini kembali menjadi Calon Penghuni.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI PERBAIKAN: Menvalidasi input kolom dokumen
     */
    private function validateKolom($kolom)
    {
        // Daftarkan jenis_dokumen apa saja yang diperbolehkan di sini
        $allowedColumns = ['ktp', 'kk', 'sk_kerja', 'foto_profil', 'ijazah']; 

        if (!in_array($kolom, $allowedColumns)) {
            abort(404, 'Jenis dokumen tidak dikenal.');
        }
    }
}