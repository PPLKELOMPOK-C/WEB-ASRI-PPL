<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\KontrakSewa;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with(['user', 'unit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $tagihans = $query->orderBy('jatuh_tempo', 'desc')->paginate(15)->withQueryString();

        $summary = [
            'total_belum_bayar'        => Tagihan::where('status', 'belum_bayar')->sum('jumlah'),
            'total_menunggu_verifikasi' => Tagihan::where('status', 'menunggu_verifikasi')->count(),
            'total_lunas_bulan_ini'     => Tagihan::where('status', 'lunas')
                ->whereMonth('tanggal_bayar', now()->month)->sum('jumlah'),
        ];

        return view('admin.tagihan.index', compact('tagihans', 'summary'));
    }

    public function show($id)
    {
        $tagihan = Tagihan::with(['user', 'unit'])->findOrFail($id);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function create()
    {
        $penghunisAktif = KontrakSewa::with(['user', 'unit'])
            ->where('status', 'aktif')
            ->get();
        return view('admin.tagihan.create', compact('penghunisAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'unit_id'    => 'required|exists:units,id',
            'jumlah'     => 'required|numeric|min:0',
            'periode'    => 'required|string|regex:/^\d{4}-\d{2}$/',
            'jatuh_tempo'=> 'required|date',
            'catatan'    => 'nullable|string|max:255',
        ]);

        // Cek tagihan periode yang sama sudah ada
        $exists = Tagihan::where('user_id', $request->user_id)
            ->where('unit_id', $request->unit_id)
            ->where('periode', $request->periode)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tagihan untuk periode ini sudah ada untuk penghuni tersebut.')->withInput();
        }

        $tagihan = Tagihan::create($request->only(['user_id', 'unit_id', 'jumlah', 'periode', 'jatuh_tempo', 'catatan']));

        // Kirim notifikasi ke penghuni
        Notifikasi::create([
            'user_id' => $tagihan->user_id,
            'judul'   => 'Tagihan Baru Tersedia',
            'pesan'   => "Tagihan sewa bulan {$tagihan->periode} sebesar Rp " . number_format($tagihan->jumlah, 0, ',', '.') . " telah diterbitkan. Jatuh tempo: {$tagihan->jatuh_tempo->format('d M Y')}.",
            'tipe'    => 'warning',
            'link'    => route('penghuni.tagihan.show', $tagihan->id),
        ]);

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dibuat dan notifikasi dikirim ke penghuni.');
    }

    public function verifikasi($id)
    {
        $tagihan = Tagihan::with('user')->findOrFail($id);

        if ($tagihan->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Tagihan tidak dalam status menunggu verifikasi.');
        }

        $tagihan->update(['status' => 'lunas']);

        Notifikasi::create([
            'user_id' => $tagihan->user_id,
            'judul'   => '✅ Pembayaran Terverifikasi',
            'pesan'   => "Pembayaran tagihan bulan {$tagihan->periode} Anda telah diverifikasi dan dinyatakan LUNAS.",
            'tipe'    => 'success',
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan tagihan dinyatakan lunas.');
    }

    public function tolakBayar(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:5|max:255']);

        $tagihan = Tagihan::with('user')->findOrFail($id);

        $tagihan->update([
            'status'      => 'belum_bayar',
            'bukti_bayar' => null,
            'tanggal_bayar'=> null,
            'catatan'     => $request->catatan,
        ]);

        Notifikasi::create([
            'user_id' => $tagihan->user_id,
            'judul'   => '❌ Bukti Pembayaran Ditolak',
            'pesan'   => "Bukti pembayaran tagihan bulan {$tagihan->periode} Anda ditolak. Alasan: {$request->catatan}. Silakan unggah ulang.",
            'tipe'    => 'danger',
            'link'    => route('penghuni.tagihan.show', $tagihan->id),
        ]);

        return back()->with('success', 'Pembayaran ditolak dan penghuni telah dinotifikasi.');
    }

    /**
     * Generate tagihan bulanan untuk semua penghuni aktif (dipanggil via scheduler/command)
     */
    public function generateBulanan()
    {
        $kontrakAktif = KontrakSewa::where('status', 'aktif')
            ->where('tanggal_selesai', '>=', now())
            ->get();

        $periode   = now()->format('Y-m');
        $generated = 0;

        foreach ($kontrakAktif as $kontrak) {
            $exists = Tagihan::where('user_id', $kontrak->user_id)
                ->where('unit_id', $kontrak->unit_id)
                ->where('periode', $periode)
                ->exists();

            if (!$exists) {
                Tagihan::create([
                    'user_id'    => $kontrak->user_id,
                    'unit_id'    => $kontrak->unit_id,
                    'jumlah'     => $kontrak->harga_per_bulan,
                    'periode'    => $periode,
                    'jatuh_tempo'=> now()->endOfMonth(),
                    'status'     => 'belum_bayar',
                ]);
                $generated++;
            }
        }

        return redirect()->back()->with('success', "$generated tagihan berhasil di-generate untuk periode $periode.");
    }
}
