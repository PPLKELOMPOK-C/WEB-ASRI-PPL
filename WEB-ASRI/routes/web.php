<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;

// Admin Controllers
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\DataPenghuniController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\TagihanController as AdminTagihan;
use App\Http\Controllers\Admin\LaporanController as AdminLaporan;
use App\Http\Controllers\Admin\JadwalSurveiController as AdminJadwal;

// Calon Penghuni Controllers
use App\Http\Controllers\CalonPenghuni\DashboardController as CalonDashboard;
use App\Http\Controllers\CalonPenghuni\PengajuanController as CalonPengajuan;
use App\Http\Controllers\CalonPenghuni\DokumenController;
use App\Http\Controllers\CalonPenghuni\JadwalSurveiController as CalonJadwal;

// Penghuni Controllers
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboard;
use App\Http\Controllers\Penghuni\TagihanController as PenghuniTagihan;
use App\Http\Controllers\Penghuni\LaporanKerusakanController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/unit', [PublicController::class, 'units'])->name('public.units');
Route::get('/unit/{unit}', [PublicController::class, 'unitDetail'])->name('public.unit.detail');
Route::get('/berita', [PublicController::class, 'news'])->name('public.news');
Route::get('/berita/{slug}', [PublicController::class, 'newsDetail'])->name('public.news.detail');

/*
|--------------------------------------------------------------------------
| Auth & Redirect Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isAdmin()) return redirect()->route('admin.dashboard');
    if ($user->isPenghuni()) return redirect()->route('penghuni.dashboard');
    return redirect()->route('calon.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Profile & Notif)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::patch('/profile/avatar', 'updateAvatar')->name('profile.avatar.update');
        Route::delete('/profile/avatar', 'destroyAvatar')->name('profile.avatar.destroy');
    });

    // Notifications
    Route::controller(NotifikasiController::class)->prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/read', 'markRead')->name('read');
        Route::post('/read-all', 'markAllRead')->name('read-all');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::delete('/destroy-all', 'destroyAll')->name('destroy-all');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Stats
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/statistik', [AdminDashboard::class, 'statistik'])->name('statistik');

    // Manajemen Utama
    Route::resource('unit', UnitController::class);
    Route::resource('berita', NewsController::class);
    
   // Manajemen Pengajuan Sewa
Route::controller(\App\Http\Controllers\Admin\PengajuanController::class)->prefix('pengajuan')->name('pengajuan.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show'); // Rute yang error tadi
    Route::patch('/{id}/status', 'updateStatus')->name('update-status');
    Route::post('/{id}/terima', 'terima')->name('terima');
    Route::post('/{id}/tolak', 'tolak')->name('tolak');
});

 // Data Penghuni
Route::controller(DataPenghuniController::class)->prefix('penghuni')->name('penghuni.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
    Route::delete('/{id}', 'destroy')->name('destroy');
    Route::put('/{id}/kick', 'kick')->name('kick');

    // TAMBAHKAN DUA BARIS INI:
    Route::get('/{id}/view/{jenis}', 'viewDokumen')->name('view');
    Route::get('/{id}/download/{jenis}', 'downloadDokumen')->name('download');
});
    // Jadwal Survei
    Route::controller(AdminJadwal::class)->prefix('jadwal-survei')->name('jadwal.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/selesai', 'selesai')->name('selesai');
        Route::post('/{id}/batalkan', 'batalkan')->name('batalkan');
        Route::get('/slots', 'getAvailableSlots')->name('slots');
    });

    // Tagihan
    Route::resource('tagihan', AdminTagihan::class)->only(['index', 'show', 'create', 'store']);
    Route::post('/tagihan/generate-bulanan', [AdminTagihan::class, 'generateBulanan'])->name('tagihan.generate');

    // Laporan Kerusakan
    Route::controller(AdminLaporan::class)->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/status', 'updateStatus')->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| Calon Penghuni Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:calon_penghuni'])->prefix('calon')->name('calon.')->group(function () {
    Route::get('/dashboard', [CalonDashboard::class, 'index'])->name('dashboard');

    // Pengajuan
    Route::controller(CalonPengajuan::class)->prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/buat/{unit}', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/batalkan', 'batalkan')->name('batalkan');
    });

    // Dokumen & Jadwal
    Route::post('/pengajuan/{pengajuan}/dokumen/upload-all', [DokumenController::class, 'storeAll'])->name('dokumen.store-all');
    Route::resource('dokumen', DokumenController::class)->only(['index', 'store', 'destroy']);
    
    Route::controller(CalonJadwal::class)->prefix('jadwal-survei')->name('jadwal.')->group(function () {
        Route::get('/{pengajuan_id}', 'index')->name('index');
        Route::post('/{pengajuan_id}', 'pilihJadwal')->name('pilih');
    });
});

/*
|--------------------------------------------------------------------------
| Penghuni Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:penghuni'])->prefix('penghuni')->name('penghuni.')->group(function () {
    Route::get('/dashboard', [PenghuniDashboard::class, 'index'])->name('dashboard');

    // Tagihan & Laporan
    Route::resource('tagihan', PenghuniTagihan::class)->only(['index', 'show']);
    Route::post('/tagihan/{id}/bayar', [PenghuniTagihan::class, 'uploadBukti'])->name('tagihan.bayar');

    Route::resource('laporan', LaporanKerusakanController::class);
    Route::post('/laporan/{id}/close', [LaporanKerusakanController::class, 'close'])->name('laporan.close');
});