<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Snap;

class TagihanController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari config/services.php
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        // Fix SSL untuk Localhost Windows agar tidak error cURL
        Config::$curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
        ];
    }

    /**
     * Menampilkan daftar tagihan milik penghuni
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $query = Tagihan::where('user_id', $userId);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->where('periode', 'like', $request->tahun . '%');
        }

        $tagihans = $query->orderBy('jatuh_tempo', 'desc')
                          ->paginate(12)
                          ->withQueryString();

        $summary = [
            'total_belum_bayar' => Tagihan::where('user_id', $userId)
                ->where('status', 'belum_bayar')->sum('jumlah'),
            'total_lunas'       => Tagihan::where('user_id', $userId)
                ->where('status', 'lunas')->count(),
            'tagihan_terlambat'  => Tagihan::where('user_id', $userId)
                ->where('status', 'belum_bayar')
                ->where('jatuh_tempo', '<', now())->count(),
        ];

        $tagihanSegera = Tagihan::where('user_id', $userId)
            ->where('status', 'belum_bayar')
            ->orderBy('jatuh_tempo')
            ->first();

        return view('penghuni.tagihan.index', [
            'tagihans'      => $tagihans,
            'summary'       => $summary,
            'tagihanSegera' => $tagihanSegera
        ]);
    }

    /**
     * Menampilkan detail tagihan dan generate Snap Token Midtrans
     */
    public function show($id): View
    {
        // Ambil data tagihan, pastikan milik user yang sedang login
        $tagihan = Tagihan::with('unit')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $snapToken = null;
        $errorMessage = null;

        if ($tagihan->status === 'belum_bayar') {
            try {
                $user = Auth::user();

                // Validasi Email (Midtrans wajib email)
                if (!$user->email) {
                    throw new \Exception("Email user kosong. Harap isi email di database.");
                }

                $params = [
                    'transaction_details' => [
                        'order_id'     => 'ASRI-' . $tagihan->id . '-' . time(), 
                        'gross_amount' => (int) $tagihan->jumlah,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email'      => $user->email,
                    ],
                ];

                $snapToken = Snap::getSnapToken($params);

            } catch (\Exception $e) {
                Log::error('Midtrans Error: ' . $e->getMessage());
                $errorMessage = $e->getMessage();
            }
        }

        // PERBAIKAN UTAMA: Menggunakan array asosiatif untuk mengirim data ke view
        // Ini akan menghilangkan error "Undefined array key 10023"
        return view('penghuni.tagihan.show', [
            'tagihan'      => $tagihan,
            'snapToken'    => $snapToken,
            'errorMessage' => $errorMessage
        ]);
    }

    /**
     * Webhook Midtrans (Opsional untuk testing otomatis)
     */
    public function handleWebhook(Request $request)
    {
        $serverKey = config('services.midtrans.serverKey');
        $signature = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderParts = explode('-', $request->order_id);
        $tagihanId = $orderParts[1] ?? null;
        $tagihan = Tagihan::find($tagihanId);

        if ($tagihan) {
            $status = $request->transaction_status;
            if (in_array($status, ['capture', 'settlement'])) {
                $tagihan->update(['status' => 'lunas', 'tanggal_bayar' => now()]);
            }
        }

        return response()->json(['message' => 'OK']);
    }
}