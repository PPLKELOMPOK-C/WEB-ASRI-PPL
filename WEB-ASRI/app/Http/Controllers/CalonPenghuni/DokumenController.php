<?php

namespace App\Http\Controllers\CalonPenghuni;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\PengajuanSewa;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DokumenController extends Controller
{
    public function index(string|int $pengajuan_id): View
    {
        $pengajuan = PengajuanSewa::with('unit')
            ->where('user_id', Auth::id())
            ->findOrFail($pengajuan_id);

        $dokumens = Dokumen::where('pengajuan_sewa_id', $pengajuan_id)
            ->get()
            ->keyBy('jenis_dokumen');

        $jenisDokumen = [
            'ktp'              => 'KTP (Kartu Tanda Penduduk)',
            'kk'               => 'KK (Kartu Keluarga)',
            'slip_gaji'        => 'Slip Gaji / Bukti Penghasilan',
            'surat_keterangan' => 'Surat Keterangan Kerja',
        ];

        return view('calon_penghuni.dokumen.index', compact('pengajuan', 'dokumens', 'jenisDokumen'));
    }

    public function storeAll(Request $request, string|int $pengajuan_id): RedirectResponse
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $pengajuan = PengajuanSewa::where('user_id', Auth::id())->findOrFail($pengajuan_id);

        if (!in_array($pengajuan->status, ['pending', 'draft', 'verifikasi_dokumen'])) {
            return back()->with('error', 'Status pengajuan saat ini tidak mengizinkan perubahan dokumen.');
        }

        $files = $request->file('files');
        $uploadedCount = 0;

        foreach ($files as $jenis => $file) {
            if ($file) {
                // Hapus file lama jika ada
                $existing = Dokumen::where('pengajuan_sewa_id', $pengajuan_id)
                    ->where('jenis_dokumen', $jenis)
                    ->first();

                if ($existing) {
                    Storage::disk('public')->delete($existing->path_file);
                    $existing->delete();
                }

                // Simpan file baru
                $path = $file->store('dokumen_persyaratan/' . $pengajuan_id, 'public');

                Dokumen::create([
                    'pengajuan_sewa_id' => $pengajuan_id,
                    'user_id'           => Auth::id(),
                    'jenis_dokumen'     => $jenis,
                    'nama_file'         => $file->getClientOriginalName(),
                    'path_file'         => $path,
                    'mime_type'         => $file->getMimeType(),
                    'ukuran_file'       => round($file->getSize() / 1024),
                    'status'            => 'uploaded',
                ]);
                $uploadedCount++;
            }
        }

        // Cek Kelengkapan Wajib
        $wajib = ['ktp', 'kk', 'slip_gaji'];
        $currentDocs = Dokumen::where('pengajuan_sewa_id', $pengajuan_id)
            ->whereIn('jenis_dokumen', $wajib)
            ->count();

        if ($currentDocs >= count($wajib) && $pengajuan->status === 'pending') {
            $pengajuan->update(['status' => 'verifikasi_dokumen']);
            
            Notifikasi::create([
                'user_id' => Auth::id(),
                'judul'   => 'Dokumen Terkirim',
                'pesan'   => 'Berkas Anda telah lengkap dan akan segera diverifikasi oleh admin.',
                'tipe'    => 'success',
            ]);
        }

        return redirect()->route('calon.pengajuan.show', $pengajuan_id)
            ->with('success', 'Berhasil mengunggah ' . $uploadedCount . ' dokumen.');
    }
}