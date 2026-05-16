<?php

namespace App\Http\Controllers\CalonPenghuni;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\Unit;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil pengajuan yang sedang berjalan (tidak termasuk yang sudah selesai/batal)
        $pengajuanAktif = PengajuanSewa::with(['unit', 'dokumens', 'jadwalSurvei'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['diterima', 'ditolak', 'dibatalkan'])
            ->latest()
            ->first();

        // Ambil riwayat pengajuan terakhir untuk tabel riwayat
        $riwayatPengajuan = PengajuanSewa::with('unit')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Rekomendasi unit tersedia secara acak
        $unitRekomendasi = Unit::where('status', 'tersedia')
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Notifikasi terbaru yang belum dibaca
        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        // Hitung langkah proses berdasarkan data terbaru
        $langkahProses = $this->getLangkahProses($pengajuanAktif);

        return view('calon_penghuni.dashboard', compact(
            'pengajuanAktif', 
            'riwayatPengajuan', 
            'unitRekomendasi',
            'notifikasiTerbaru', 
            'langkahProses'
        ));
    }

    /**
     * Logika sinkronisasi progress bar dengan database ASRI
     */
    private function getLangkahProses(?PengajuanSewa $pengajuan): array
    {
        $langkah = [
            ['label' => 'Isi Formulir',    'status' => 'waiting'],
            ['label' => 'Upload Dokumen',   'status' => 'waiting'],
            ['label' => 'Verifikasi Admin',  'status' => 'waiting'],
            ['label' => 'Jadwal Survei',    'status' => 'waiting'],
            ['label' => 'Persetujuan Akhir', 'status' => 'waiting'],
        ];

        if (!$pengajuan) return $langkah;

        // Tentukan index langkah yang saat ini sedang aktif (0-4)
        $currentStep = 0;

        // Status Map berdasarkan logika bisnis ASRI
        if ($pengajuan->status === 'pending') {
            // Jika dokumen belum mencapai syarat minimal (3 file), tetap di step upload
            $currentStep = ($pengajuan->dokumens->count() >= 3) ? 2 : 1;
        } elseif ($pengajuan->status === 'verifikasi_dokumen') {
            $currentStep = 2; // Fokus pada Verifikasi Admin
        } elseif ($pengajuan->status === 'jadwal_survei') {
            $currentStep = 3; // Fokus pada Jadwal Survei
        } elseif ($pengajuan->status === 'diterima') {
            $currentStep = 4; // Persetujuan Akhir Selesai
        }

        // Loop untuk menentukan status visual (done, active, atau waiting)
        for ($i = 0; $i < count($langkah); $i++) {
            if ($i < $currentStep) {
                $langkah[$i]['status'] = 'done';
            } elseif ($i == $currentStep) {
                $langkah[$i]['status'] = 'active';
            } else {
                $langkah[$i]['status'] = 'waiting';
            }
        }

        // Karena pengajuan sudah ada di DB, langkah pertama (Isi Formulir) selalu centang
        $langkah[0]['status'] = 'done';

        return $langkah;
    }
}