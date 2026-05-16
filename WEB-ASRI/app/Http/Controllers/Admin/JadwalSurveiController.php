<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalSurvei;
use App\Models\PengajuanSewa;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalSurveiController extends Controller
{
    public function index(Request $request)
    {
        // Eager loading relasi utama
        $query = JadwalSurvei::with(['pengajuanSewa.user', 'pengajuanSewa.unit', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_survei', $request->tanggal);
        }

        // Ambil jadwal hari ini yang terkonfirmasi dengan pengamanan eager loading relasi
        $jadwalHariIni = JadwalSurvei::whereDate('tanggal_survei', today())
            ->where('status', 'dikonfirmasi')
            ->with(['pengajuanSewa.user', 'pengajuanSewa.unit'])
            ->orderBy('tanggal_survei')
            ->get();

        $jadwals = $query->orderBy('tanggal_survei', 'desc')->paginate(15)->withQueryString();

        return view('admin.jadwal.index', compact('jadwals', 'jadwalHariIni'));
    }

    public function konfirmasi($id)
    {
        $jadwal = JadwalSurvei::with(['pengajuanSewa.user', 'pengajuanSewa.unit'])->findOrFail($id);

        if ($jadwal->status !== 'pending') {
            return back()->with('error', 'Jadwal ini tidak dalam status pending.');
        }

        // Proteksi bentrok jadwal (toleransi selisih 30 menit)
        $bentrok = JadwalSurvei::where('id', '!=', $id)
            ->where('status', 'dikonfirmasi')
            ->whereBetween('tanggal_survei', [
                Carbon::parse($jadwal->tanggal_survei)->subMinutes(30),
                Carbon::parse($jadwal->tanggal_survei)->addMinutes(30),
            ])
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Terdapat jadwal survei lain dalam waktu berdekatan. Pilih slot waktu berbeda.');
        }

        $jadwal->update(['status' => 'dikonfirmasi']);

        // Kirim notifikasi hanya jika data pengajuanSewa dan user tersedia
        if ($jadwal->pengajuanSewa && $jadwal->pengajuanSewa->user_id) {
            $namaUnit = $jadwal->pengajuanSewa->unit?->nama_unit ?? 'Rusun';
            Notifikasi::create([
                'user_id' => $jadwal->pengajuanSewa->user_id,
                'judul'   => '📅 Jadwal Survei Dikonfirmasi',
                'pesan'   => "Jadwal survei unit {$namaUnit} pada " .
                    Carbon::parse($jadwal->tanggal_survei)->format('d M Y, H:i') . " telah dikonfirmasi.",
                'tipe'    => 'success',
            ]);
        }

        return back()->with('success', 'Jadwal survei berhasil dikonfirmasi.');
    }

    public function selesai($id)
    {
        $jadwal = JadwalSurvei::findOrFail($id);
        $jadwal->update(['status' => 'selesai']);

        return back()->with('success', 'Survei ditandai selesai.');
    }

    public function batalkan(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:255']);

        $jadwal = JadwalSurvei::with('pengajuanSewa')->findOrFail($id);
        $jadwal->update([
            'status'  => 'dibatalkan',
            'catatan' => $request->catatan,
        ]);

        // Kembalikan status alur sewa ke verifikasi_dokumen agar user bisa reschedule
        if ($jadwal->pengajuanSewa) {
            $jadwal->pengajuanSewa->update(['status' => 'verifikasi_dokumen']);
        }

        // Tentukan target user_id penerima notifikasi secara adaptif
        $targetUserId = $jadwal->user_id ?? ($jadwal->pengajuanSewa?->user_id ?? null);

        if ($targetUserId) {
            Notifikasi::create([
                'user_id' => $targetUserId,
                'judul'   => 'Jadwal Survei Dibatalkan',
                'pesan'   => "Jadwal survei Anda dibatalkan. Silakan hubungi admin untuk reschedule." . ($request->catatan ? " Alasan: {$request->catatan}" : ''),
                'tipe'    => 'warning',
            ]);
        }

        return back()->with('success', 'Jadwal survei dibatalkan.');
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate(['tanggal' => 'required|date|after:today']);

        $tanggal        = Carbon::parse($request->tanggal);
        $allSlots       = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
        $slotsTerpakai  = JadwalSurvei::whereDate('tanggal_survei', $tanggal)
            ->where('status', 'dikonfirmasi')
            ->get()
            ->map(fn($j) => Carbon::parse($j->tanggal_survei)->format('H:i'))
            ->toArray();

        $slotsAvailable = array_filter($allSlots, fn($s) => !in_array($s, $slotsTerpakai));

        return response()->json(['slots' => array_values($slotsAvailable)]);
    }
}