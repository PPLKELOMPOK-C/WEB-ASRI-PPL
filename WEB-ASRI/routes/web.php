<?php
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuan;
use App\Http\Controllers\Admin\TagihanController as AdminTagihan;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporan;
use App\Http\Controllers\Admin\JadwalSurveiController as AdminJadwal;
use App\Http\Controllers\CalonPenghuni\DashboardController as CalonDashboard;
use App\Http\Controllers\CalonPenghuni\PengajuanController as CalonPengajuan;
use App\Http\Controllers\CalonPenghuni\DokumenController;
use App\Http\Controllers\CalonPenghuni\JadwalSurveiController as CalonJadwal;
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboard;
use App\Http\Controllers\Penghuni\TagihanController;
use App\Http\Controllers\Penghuni\LaporanKerusakanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DataPenghuniController;
use App\Http\Controllers\ProfileController;

// ========================================================
// PUBLIC ROUTES (Tanpa Login)
// ========================================================
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/unit', [PublicController::class, 'units'])->name('public.units');
Route::get('/unit/{unit}', [PublicController::class, 'unitDetail'])->name('public.unit.detail');
Route::get('/berita', [PublicController::class, 'news'])->name('public.news');
Route::get('/berita/{slug}', [PublicController::class, 'newsDetail'])->name('public.news.detail');
// Ubah nama rutenya jadi pakai titik
Route::get('/unit/{unit}', [PublicController::class, 'unitDetail'])->name('public.unit.detail');

// ========================================================
// AUTH ROUTES (dari Breeze)
// ========================================================
require __DIR__.'/auth.php';

// ========================================================
// REDIRECT BERDASARKAN ROLE SETELAH LOGIN
// ========================================================
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->isPenghuni()) {
        return redirect()->route('penghuni.dashboard');
    }

    return redirect()->route('calon.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ========================================================
// NOTIFIKASI (semua role yang login)
// ========================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
    Route::delete('/notifikasi/destroy-all', [NotifikasiController::class, 'destroyAll'])->name('notifikasi.destroy-all');
});

// ========================================================
// ADMIN ROUTES
// ========================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Manajemen Unit
    Route::resource('unit', UnitController::class);

    // Manajemen Dokumen
    Route::resource('dokumen', DokumenController::class)
    ->only(['index','show'])
    ->names('admin.dokumen');
    
    // Manajemen Pengajuan Sewa
Route::get('/pengajuan', [AdminPengajuan::class, 'index'])->name('pengajuan.index');
Route::get('/pengajuan/{id}', [AdminPengajuan::class, 'show'])->name('pengajuan.show');

// Gunakan patch untuk update status sebagian data
Route::patch('/pengajuan/{id}/status', [AdminPengajuan::class, 'updateStatus'])->name('pengajuan.update-status');

Route::post('/pengajuan/{id}/terima', [AdminPengajuan::class, 'terima'])->name('pengajuan.terima');
Route::post('/pengajuan/{id}/tolak', [AdminPengajuan::class, 'tolak'])->name('pengajuan.tolak');
    // Jadwal Survei
    Route::get('/jadwal-survei', [AdminJadwal::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal-survei/{id}/konfirmasi', [AdminJadwal::class, 'konfirmasi'])->name('jadwal.konfirmasi');

    // Tagihan
    Route::resource('tagihan', AdminTagihan::class)->only(['index', 'show', 'create', 'store']);
    Route::post('/tagihan/{id}/verifikasi', [AdminTagihan::class, 'verifikasi'])->name('tagihan.verifikasi');
    Route::post('/tagihan/{id}/tolak', [AdminTagihan::class, 'tolakBayar'])->name('tagihan.tolak');

    // Laporan Kerusakan
    Route::get('/laporan', [AdminLaporan::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{id}', [AdminLaporan::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{id}/status', [AdminLaporan::class, 'updateStatus'])->name('laporan.status');

    // Berita & Pengumuman
    Route::resource('berita', NewsController::class);
    Route::post('/berita/{id}/publish', [NewsController::class, 'togglePublish'])->name('berita.publish');
    Route::post('/berita/{id}/publish', [NewsController::class, 'togglePublish'])->name('berita.toggle-publish');

    // Statistik
    Route::get('/statistik', [AdminDashboard::class, 'statistik'])->name('statistik');
});

Route::post('/jadwal-survei/{id}/selesai', [AdminJadwal::class, 'selesai'])->name('jadwal.selesai');
Route::post('/jadwal-survei/{id}/batalkan', [AdminJadwal::class, 'batalkan'])->name('jadwal.batalkan');
Route::get('/jadwal-survei/slots', [AdminJadwal::class, 'getAvailableSlots'])->name('jadwal.slots');
Route::post('/tagihan/generate-bulanan', [AdminTagihan::class, 'generateBulanan'])->name('tagihan.generate');

//data penghuni

Route::middleware(['auth', 'admin:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/penghuni', [DataPenghuniController::class, 'index'])->name('penghuni.index');
    Route::get('/penghuni/{id}', [DataPenghuniController::class, 'show'])->name('penghuni.show');
    Route::get('/penghuni/view/{id}/{kolom}', [DataPenghuniController::class, 'viewBerkas'])->name('penghuni.view');
    Route::get('/penghuni/download/{id}/{kolom}', [DataPenghuniController::class, 'downloadBerkas'])->name('penghuni.download');

});

//kick penghuni
// Tambahkan ini di dalam grup Route admin
Route::put('/penghuni/kick/{id}', [DataPenghuniController::class, 'kick'])->name('admin.penghuni.kick');

// ========================================================
// CALON PENGHUNI ROUTES
// ========================================================
Route::middleware(['auth', 'role:calon_penghuni'])->prefix('calon')->name('calon.')->group(function () {

    Route::get('/dashboard', [CalonDashboard::class, 'index'])->name('dashboard');

    // Pengajuan Sewa
    Route::get('/pengajuan', [CalonPengajuan::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/buat/{unit}', [CalonPengajuan::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [CalonPengajuan::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{id}', [CalonPengajuan::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{id}/batalkan', [CalonPengajuan::class, 'batalkan'])->name('pengajuan.batalkan');

    // Upload Dokumen
    Route::get('/dokumen/{pengajuan_id}', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::post('/dokumen/{pengajuan_id}', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::delete('/dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::post('/pengajuan/{pengajuan}/dokumen/upload-all', [DokumenController::class, 'storeAll'])
    ->name('calon.dokumen.store-all');
    

    // Jadwal Survei
    Route::get('/jadwal-survei/{pengajuan_id}', [CalonJadwal::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal-survei/{pengajuan_id}', [CalonJadwal::class, 'pilihJadwal'])->name('jadwal.pilih');
});

// ========================================================
// PENGHUNI ROUTES
// ========================================================
Route::middleware(['auth', 'role:penghuni'])->prefix('penghuni')->name('penghuni.')->group(function () {

    Route::get('/dashboard', [PenghuniDashboard::class, 'index'])->name('dashboard');

    // Tagihan
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/{id}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'uploadBukti'])->name('tagihan.bayar');

    // Laporan Kerusakan
    Route::get('/laporan', [LaporanKerusakanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/buat', [LaporanKerusakanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [LaporanKerusakanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanKerusakanController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{id}/close', [LaporanKerusakanController::class, 'close'])->name('laporan.close');
});

// PROFILE//
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
