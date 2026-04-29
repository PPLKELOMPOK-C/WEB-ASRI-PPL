<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\KontrakSewa;
use App\Models\Tagihan;
use App\Models\LaporanKerusakan;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth; // Wajib di-import untuk hapus redline
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama penghuni.
     */
    public function index(): View
    {
        // Menggunakan Facade Auth dan DocBlock agar VS Code mengenali property 'id'
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Kontrak aktif
        $kontrakAktif = KontrakSewa::with('unit')
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        // Tagihan belum bayar (prioritas berdasarkan jatuh tempo terdekat)
        $tagihanBelumBayar = Tagihan::where('user_id', $user->id)
            ->where('status', 'belum_bayar')
            ->orderBy('jatuh_tempo')
            ->first();

        // Count semua tagihan belum lunas
        $tagihanBelumLunas = Tagihan::where('user_id', $user->id)
            ->whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])
            ->count();

        // Tagihan terbaru (riwayat singkat)
        $tagihanTerbaru = Tagihan::where('user_id', $user->id)
            ->orderBy('jatuh_tempo', 'desc')
            ->take(4)
            ->get();

        // Laporan kerusakan aktif
        $laporanAktif = LaporanKerusakan::where('user_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Notifikasi belum dibaca
        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        // Kalkulasi Info kontrak
        $infoKontrak = null;
        if ($kontrakAktif) {
            // Pastikan tanggal_selesai dan tanggal_mulai sudah di-cast sebagai Carbon di Model
            $sisaHari    = now()->diffInDays($kontrakAktif->tanggal_selesai, false);
            $totalHari   = $kontrakAktif->tanggal_mulai->diffInDays($kontrakAktif->tanggal_selesai);
            
            $progressPct = $totalHari > 0
                ? min(100, round((($totalHari - $sisaHari) / $totalHari) * 100))
                : 0;

            $infoKontrak = [
                'sisa_hari'    => max(0, $sisaHari),
                'progress'     => $progressPct,
                'hampir_habis' => $sisaHari <= 30 && $sisaHari > 0,
            ];
        }

        // Statistik pribadi penghuni
        $statsPersonal = [
            'total_bayar'   => Tagihan::where('user_id', $user->id)->where('status', 'lunas')->sum('jumlah'),
            'total_laporan' => LaporanKerusakan::where('user_id', $user->id)->count(),
            'lama_menghuni' => $kontrakAktif ? $kontrakAktif->tanggal_mulai->diffInMonths(now()) : 0,
        ];

        return view('penghuni.dashboard', compact(
            'kontrakAktif', 'tagihanBelumBayar', 'tagihanBelumLunas',
            'tagihanTerbaru', 'laporanAktif', 'notifikasiTerbaru',
            'infoKontrak', 'statsPersonal'
        ));
    }
}