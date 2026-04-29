<?php

namespace App\Http\Controllers\CalonPenghuni;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\Unit;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth; // Wajib di-import untuk hapus redline
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Menggunakan Facade Auth agar Intelephense mengenali object user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pengajuan aktif (yang belum selesai)
        $pengajuanAktif = PengajuanSewa::with(['unit', 'dokumens', 'jadwalSurvei'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['diterima', 'ditolak', 'dibatalkan'])
            ->latest()
            ->first();

        // Riwayat pengajuan
        $riwayatPengajuan = PengajuanSewa::with('unit')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Unit yang direkomendasikan (tersedia)
        $unitRekomendasi = Unit::where('status', 'tersedia')
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Notifikasi belum dibaca
        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        // Hitung langkah proses pengajuan
        $langkahProses = $this->getLangkahProses($pengajuanAktif);

        return view('calon_penghuni.dashboard', compact(
            'pengajuanAktif', 'riwayatPengajuan', 'unitRekomendasi',
            'notifikasiTerbaru', 'langkahProses'
        ));
    }

    private function getLangkahProses(?PengajuanSewa $pengajuan): array
    {
        $langkah = [
            ['label' => 'Isi Formulir',    'status' => 'done'],
            ['label' => 'Upload Dokumen',   'status' => 'waiting'],
            ['label' => 'Verifikasi Admin',  'status' => 'waiting'],
            ['label' => 'Jadwal Survei',    'status' => 'waiting'],
            ['label' => 'Persetujuan Akhir', 'status' => 'waiting'],
        ];

        if (!$pengajuan) return $langkah;

        $statusMap = [
            'pending'            => 1,
            'verifikasi_dokumen' => 2,
            'jadwal_survei'      => 3,
            'diterima'           => 4,
        ];

        // Pakai null coalescing agar tidak error jika status tidak ada di map
        $step = $statusMap[$pengajuan->status] ?? 0;

        for ($i = 0; $i < count($langkah); $i++) {
            if ($i < $step) {
                $langkah[$i]['status'] = 'done';
            } elseif ($i == $step) {
                $langkah[$i]['status'] = 'active';
            } else {
                $langkah[$i]['status'] = 'waiting';
            }
        }

        return $langkah;
    }
}