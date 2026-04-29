<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\KontrakSewa;
use App\Models\Tagihan;
use App\Models\Notifikasi;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanSewa::with(['user', 'unit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"));
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $pengajuans = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $statusCounts = PengajuanSewa::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.pengajuan.index', compact('pengajuans', 'statusCounts'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanSewa::with([
            'user', 'unit', 'dokumens', 'jadwalSurvei', 'kontrakSewa'
        ])->findOrFail($id);

        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:pending,verifikasi_dokumen,jadwal_survei,diterima,ditolak,dibatalkan',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pengajuan = PengajuanSewa::with('user', 'unit')->findOrFail($id);
        $oldStatus = $pengajuan->status;

        $pengajuan->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        // Kirim notifikasi ke user
        $this->kirimNotifikasiStatus($pengajuan, $request->status);

        return back()->with('success', "Status pengajuan berhasil diubah dari {$oldStatus} menjadi {$request->status}.");
    }

    public function terima(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai'  => 'required|date',
            'harga_per_bulan'=> 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanSewa::with('user', 'unit')->findOrFail($id);

        if (!in_array($pengajuan->status, ['jadwal_survei', 'verifikasi_dokumen', 'pending'])) {
            return back()->with('error', 'Pengajuan tidak dapat diterima pada status ini.');
        }

        $tanggalMulai  = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = $tanggalMulai->copy()->addMonths($pengajuan->durasi_sewa);

        // Update pengajuan
        $pengajuan->update([
            'status'        => 'diterima',
            'tanggal_mulai' => $tanggalMulai,
        ]);

        // Buat kontrak sewa
        $kontrak = KontrakSewa::create([
            'user_id'            => $pengajuan->user_id,
            'unit_id'            => $pengajuan->unit_id,
            'pengajuan_sewa_id'  => $pengajuan->id,
            'tanggal_mulai'      => $tanggalMulai,
            'tanggal_selesai'    => $tanggalSelesai,
            'harga_per_bulan'    => $request->harga_per_bulan,
            'status'             => 'aktif',
        ]);

        // Update status unit menjadi dihuni
        $pengajuan->unit->update(['status' => 'dihuni']);

        // Update role user menjadi penghuni
        $pengajuan->user->update(['role' => 'penghuni']);

        // Generate tagihan bulan pertama
        Tagihan::create([
            'user_id'    => $pengajuan->user_id,
            'unit_id'    => $pengajuan->unit_id,
            'jumlah'     => $request->harga_per_bulan,
            'periode'    => $tanggalMulai->format('Y-m'),
            'jatuh_tempo'=> $tanggalMulai->copy()->addDays(7),
            'status'     => 'belum_bayar',
        ]);

        // Notifikasi
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'judul'   => '🎉 Pengajuan Sewa Diterima!',
            'pesan'   => "Selamat! Pengajuan sewa unit {$pengajuan->unit->nama_unit} Anda telah diterima. Kontrak berlaku mulai {$tanggalMulai->format('d M Y')}.",
            'tipe'    => 'success',
            'link'    => route('penghuni.dashboard'),
        ]);

        return back()->with('success', "Pengajuan diterima! Kontrak dan tagihan pertama telah dibuat.");
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|min:10|max:500',
        ]);

        $pengajuan = PengajuanSewa::with('user', 'unit')->findOrFail($id);

        $pengajuan->update([
            'status'        => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'judul'   => 'Pengajuan Sewa Ditolak',
            'pesan'   => "Pengajuan sewa unit {$pengajuan->unit->nama_unit} Anda ditolak. Alasan: {$request->catatan_admin}",
            'tipe'    => 'danger',
            'link'    => route('calon.pengajuan.show', $pengajuan->id),
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak dan pengguna telah dinotifikasi.');
    }

    private function kirimNotifikasiStatus(PengajuanSewa $pengajuan, string $status): void
    {
        $messages = [
            'verifikasi_dokumen' => ['Dokumen Sedang Diverifikasi', "Dokumen pengajuan sewa Anda sedang dalam proses verifikasi oleh admin.", 'info'],
            'jadwal_survei'      => ['Jadwal Survei Dibuat', "Pengajuan Anda lolos verifikasi! Silakan pilih jadwal survei unit.", 'success'],
            'dibatalkan'         => ['Pengajuan Dibatalkan', "Pengajuan sewa unit {$pengajuan->unit->nama_unit} Anda telah dibatalkan.", 'warning'],
        ];

        if (isset($messages[$status])) {
            [$judul, $pesan, $tipe] = $messages[$status];
            Notifikasi::create([
                'user_id' => $pengajuan->user_id,
                'judul'   => $judul,
                'pesan'   => $pesan,
                'tipe'    => $tipe,
                'link'    => route('calon.pengajuan.show', $pengajuan->id),
            ]);
        }
    }
}
