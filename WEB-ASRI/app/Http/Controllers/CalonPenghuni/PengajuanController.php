<?php

namespace App\Http\Controllers\CalonPenghuni;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\Unit;
use App\Models\Notifikasi;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    // Fungsi yang tadinya hilang dan bikin error
    public function index()
    {
        $pengajuans = PengajuanSewa::with(['unit', 'dokumens', 'jadwalSurvei'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('calon_penghuni.pengajuan.index', compact('pengajuans'));
    }

    public function create(Unit $unit)
    {
        if ($unit->status !== 'tersedia') {
            return redirect()->route('public.units')
                ->with('error', 'Unit ini sudah tidak tersedia.');
        }

        $pengajuanAktif = PengajuanSewa::where('user_id', Auth::id())
            ->whereNotIn('status', ['diterima', 'ditolak', 'dibatalkan'])
            ->exists();

        if ($pengajuanAktif) {
            return redirect()->route('calon.pengajuan.index')
                ->with('error', 'Anda masih memiliki pengajuan aktif.');
        }

        return view('calon_penghuni.pengajuan.create', compact('unit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id'       => 'required|exists:units,id',
            'durasi_sewa'   => 'required|integer|min:1|max:72',
            'tanggal_mulai' => 'required|date|after:today',
            'nik'           => 'required|digits:16',
            'no_hp'         => 'required|string|max:15',
            'file_ktp'      => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'file_kk'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'file_slip_gaji'=> 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        try {
            return DB::transaction(function () use ($request, $unit) {
                // Simpan Pengajuan (Data NIK & No HP Masuk Sini)
                $pengajuan = PengajuanSewa::create([
                    'user_id'       => Auth::id(),
                    'unit_id'       => $request->unit_id,
                    'nik'           => $request->nik,
                    'no_hp'         => $request->no_hp,
                    'durasi_sewa'   => $request->durasi_sewa,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'status'        => 'pending',
                    'submitted_at'  => now(),
                ]);

                // Proses Upload Dokumen
                $daftarDokumen = [
                    'ktp'       => 'file_ktp',
                    'kk'        => 'file_kk',
                    'slip_gaji' => 'file_slip_gaji',
                ];

                foreach ($daftarDokumen as $jenis => $inputName) {
                    if ($request->hasFile($inputName)) {
                        $file = $request->file($inputName);
                        $namaFile = $jenis . '_' . Auth::id() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('pendaftaran', $namaFile, 'public');

                        Dokumen::create([
                            'pengajuan_sewa_id' => $pengajuan->id,
                            'user_id'           => Auth::id(),
                            'jenis_dokumen'     => $jenis,
                            'nama_file'         => $namaFile,
                            'path_file'         => $path,
                            'mime_type'         => $file->getClientMimeType(),
                            'ukuran_file'       => round($file->getSize() / 1024),
                            'status'            => 'uploaded',
                        ]);
                    }
                }

                Notifikasi::create([
                    'user_id' => Auth::id(),
                    'judul'   => '📋 Pengajuan Berhasil',
                    'pesan'   => "Pengajuan sewa unit {$unit->nama_unit} berhasil dikirim.",
                    'tipe'    => 'info',
                ]);

                return redirect()->route('calon.pengajuan.index')
                    ->with('success', 'Pengajuan dan dokumen berhasil dikirim!');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pengajuan = PengajuanSewa::with(['unit', 'dokumens', 'jadwalSurvei'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('calon_penghuni.pengajuan.show', compact('pengajuan'));
    }

    public function batalkan($id)
    {
        $pengajuan = PengajuanSewa::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'draft'])
            ->findOrFail($id);

        $pengajuan->update(['status' => 'dibatalkan']);

        return redirect()->route('calon.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}