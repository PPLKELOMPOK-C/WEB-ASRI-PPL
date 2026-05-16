<?php

namespace App\Http\Controllers\CalonPenghuni;

use App\Http\Controllers\Controller;
use App\Models\JadwalSurvei;
use App\Models\Unit;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class JadwalSurveiController extends Controller
{
    /**
     * Menampilkan Katalog Unit untuk dipilih jadwal surveinya
     */
    public function indexMandiri(Request $request): View
    {
        // 1. Ambil data wilayah unik untuk filter di header
        $wilayahs = Unit::distinct()->pluck('wilayah');

        // 2. Query Unit dengan filter search dan wilayah
        $query = Unit::where('status', 'tersedia');

        // Filter Keyword (Gedung/Blok/Nama)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('gedung', 'like', '%' . $request->search . '%')
                  ->orWhere('blok', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_unit', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Wilayah
        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->wilayah);
        }

        // Filter Harga Max
        if ($request->filled('harga_max')) {
            $query->where('harga_sewa', '<=', $request->harga_max);
        }

        $units = $query->paginate(9);

        // 3. Ambil riwayat survei milik user ini
        $riwayatSurvei = JadwalSurvei::where('user_id', Auth::id())
            ->with('unit')
            ->latest()
            ->get();

        return view('calon_penghuni.jadwal.index', compact('units', 'wilayahs', 'riwayatSurvei'));
    }

    /**
     * Menampilkan form input tanggal & jam (Halaman baru setelah pilih unit)
     */
    public function buat($unit_id): View
    {
        $unit = Unit::findOrFail($unit_id);
        
        return view('calon_penghuni.jadwal.buat', compact('unit'));
    }

    /**
     * Menyimpan pengajuan jadwal survei mandiri
     */
    public function simpanMandiri(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam'     => 'required',
        ]);

        // Ambil data unit untuk keperluan notifikasi
        $unit = Unit::findOrFail($request->unit_id);

        // Gabungkan tanggal dan jam menjadi Carbon instance
        $tanggalFull = Carbon::parse($request->tanggal . ' ' . $request->jam);

        // --- VALIDASI OPERASIONAL ---

        // 1. Cek Hari (Senin-Sabtu). Sunday = 0
        if ($tanggalFull->isSunday()) {
            return back()->with('error', 'Kantor libur pada hari Minggu. Silakan pilih Senin sampai Sabtu.');
        }

        // 2. Cek Jam (08:00 - 15:00)
        $jamMenit = $tanggalFull->format('H:i');
        if ($jamMenit < '08:00' || $jamMenit > '15:00') {
            return back()->with('error', 'Jam operasional survei adalah 08:00 s/d 15:00.');
        }

        // 3. Cek Bentrok (30 menit interval)
        $bentrok = JadwalSurvei::where('status', 'dikonfirmasi')
            ->whereBetween('tanggal_survei', [
                $tanggalFull->copy()->subMinutes(29),
                $tanggalFull->copy()->addMinutes(29),
            ])
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Slot waktu ini sudah terisi jadwal lain. Mohon pilih jam berbeda.');
        }

        // --- SIMPAN DATA ---
        // pengajuan_sewa_id secara otomatis akan bernilai NULL karena tidak kita masukkan di sini
        // Pastikan 'unit_id' sudah ada di $fillable pada Model JadwalSurvei
        JadwalSurvei::create([
            'user_id'           => Auth::id(),
            'unit_id'           => $request->unit_id,
            'pengajuan_sewa_id' => null, 
            'tanggal_survei'    => $tanggalFull,
            'status'            => 'pending',
        ]);

        // Buat Notifikasi untuk User
        Notifikasi::create([
            'user_id' => Auth::id(),
            'judul'   => '📅 Jadwal Survei Diajukan',
            'pesan'   => "Permohonan survei unit " . $unit->nama_unit . " pada " . $tanggalFull->format('d M Y, H:i') . " WIB telah dikirim.",
            'tipe'    => 'info',
        ]);

        return redirect()->route('calon.survei.index')->with('success', 'Jadwal survei berhasil diajukan! Tunggu konfirmasi admin.');
    }
}