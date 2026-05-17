@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="asri-wrapper">
    <div class="asri-container">
        
        {{-- Flash Alert System --}}
        @if (session('success'))
        <div id="asri-alert" class="toast-alert alert-success">
            <i class="ri-checkbox-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if (session('error'))
        <div id="asri-alert" class="toast-alert alert-error">
            <i class="ri-error-warning-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Top Section: Header & Quick Filters --}}
        <div class="asri-header-row">
            <div class="header-text">
                <h1 class="page-title">Kelola Jadwal Survei</h1>
                <p class="page-subtitle">Konfirmasi, atur, dan pantau kunjungan calon penghuni rusun.</p>
            </div>
            
            {{-- Filter Form --}}
            <form action="{{ route('admin.jadwal.index') }}" method="GET" class="filter-card">
                <div class="filter-group">
                    <select name="status" class="asri-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>✅ Dikonfirmasi</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>🔒 Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                    </select>
                </div>
                <div class="filter-group">
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="asri-input-date" onchange="this.form.submit()">
                </div>
                @if(request()->filled('status') || request()->filled('tanggal'))
                    <a href="{{ route('admin.jadwal.index') }}" class="btn-clear" title="Reset Filter">
                        <i class="ri-refresh-line"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- SECTION 1: AGENDA HARI INI --}}
        <div class="asri-card info-panel-today">
            <h2 class="section-title text-white"><i class="ri-calendar-todo-line"></i> Agenda Survei Terkonfirmasi Hari Ini</h2>
            @if($jadwalHariIni->isEmpty())
                <div class="empty-today-wrapper">
                    <i class="ri-calendar-check-line"></i>
                    <p class="empty-text text-light">Tidak ada jadwal survei yang dikonfirmasi untuk hari ini.</p>
                </div>
            @else
                <div class="today-grid">
                    @foreach($jadwalHariIni as $hariIni)
                    <div class="today-item-card">
                        <div class="today-time">
                            <i class="ri-time-line"></i> {{ \Carbon\Carbon::parse($hariIni->tanggal_survei)->format('H:i') }} WIB
                        </div>
                        <div class="today-body">
                            <h4>{{ $hariIni->pengajuanSewa?->user?->name ?? 'Data Pemohon Hilang' }}</h4>
                            <p><i class="ri-building-line"></i> Unit: {{ $hariIni->pengajuanSewa?->unit?->nama_unit ?? 'Unit Tidak Diketahui' }}</p>
                        </div>
                        <div class="today-action">
                            <form action="{{ route('admin.jadwal.selesai', $hariIni->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action-check" title="Tandai Selesai">
                                    <i class="ri-check-double-line"></i> Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SECTION 2: DATA TABEL UTAMA --}}
        <div class="asri-card table-card">
            <h2 class="section-title text-dark"><i class="ri-file-list-3-line"></i> Daftar Seluruh Pengajuan Jadwal</h2>
            
            <div class="table-responsive">
                <table class="asri-table">
                    <thead>
                        <tr>
                            <th style="width: 25%">Calon Penghuni</th>
                            <th style="width: 15%">Unit Rusun</th>
                            <th style="width: 25%">Waktu Kunjungan</th>
                            <th style="width: 15%">Status</th>
                            <th style="width: 20%" class="text-center">Aksi Operasional</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($jadwals->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-400">Tidak ditemukan data jadwal survei.</td>
                            </tr>
                        @else
                            @foreach($jadwals as $jadwal)
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <span class="user-name">{{ $jadwal->pengajuanSewa?->user?->name ?? 'User Tidak Ditemukan' }}</span>
                                        <span class="user-sub">{{ $jadwal->pengajuanSewa?->user?->email ?? 'Hubungan data terputus' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="unit-badge">{{ $jadwal->pengajuanSewa?->unit?->nama_unit ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="date-time-cell">
                                        <strong class="date-text">{{ \Carbon\Carbon::parse($jadwal->tanggal_survei)->translatedFormat('d M Y') }}</strong>
                                        <span class="time-text">Pukul {{ \Carbon\Carbon::parse($jadwal->tanggal_survei)->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill status-{{ $jadwal->status }}">
                                        @if($jadwal->status == 'pending') ⏳ Pending
                                        @elseif($jadwal->status == 'dikonfirmasi') ✅ Dikonfirmasi
                                        @elseif($jadwal->status == 'selesai') 🔒 Selesai
                                        @else ❌ Batal
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-wrapper">
                                        @if($jadwal->status == 'pending')
                                            <form action="{{ route('admin.jadwal.konfirmasi', $jadwal->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-table btn-table-approve">
                                                    <i class="ri-check-line"></i> Konfirmasi
                                                </button>
                                            </form>
                                            
                                            <button type="button" class="btn-table btn-table-cancel" onclick="openCancelModal({{ $jadwal->id }})">
                                                <i class="ri-close-line"></i> Batalkan
                                            </button>
                                        @elseif($jadwal->status == 'dikonfirmasi')
                                            <form action="{{ route('admin.jadwal.selesai', $jadwal->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-table btn-table-done">
                                                    <i class="ri-checkbox-circle-line"></i> Set Selesai
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted-status">- Selesai -</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="asri-pagination-wrapper">
                {{ $jadwals->links() }}
            </div>
        </div>

    </div>
</div>

{{-- MODAL BOX PEMBATALAN --}}
<div id="cancel-modal" class="modal-backdrop">
    <div class="modal-box">
        <h3 class="modal-title"><i class="ri-error-warning-line"></i> Batalkan Jadwal Kunjungan</h3>
        <p class="modal-desc">Berikan catatan alasan pembatalan agar pemohon dapat mengetahuinya pada log sistem/notifikasi.</p>
        
        <form id="cancel-form" method="POST" action="">
            @csrf
            <div class="input-container">
                <textarea name="catatan" placeholder="Masukkan alasan pembatalan..." rows="4" required class="asri-textarea"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeCancelModal()" class="btn-modal-back">Kembali</button>
                <button type="submit" class="btn-modal-danger">Ya, Batalkan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* ==========================================================================
       PENGATURAN BREAKOUT/FULLSCREEN UTK MENEMBUS CONTAINER BAWAN LAYOUTS.APP 
       ========================================================================== */
    body, html {
        overflow-x: hidden; /* Mencegah horizontal scroll akibat efek breakout */
    }

    /* Mengakali container pembungkus default layouts.app agar melebar penuh */
    main, .content-wrapper, .container {
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Styling Utama Komponen Jadwal */
    .asri-wrapper { 
        background-color: #FDFDFB; 
        min-height: 100vh; 
        padding: 30px 40px; /* Padding samping diperlebar agar seimbang di layar besar */
        font-family: 'Plus Jakarta Sans', sans-serif !important; 
        color: #334155; 
        width: 100%;
        box-sizing: border-box;
    }
    .asri-wrapper * { font-family: 'Plus Jakarta Sans', sans-serif !important; }
    
    /* Container diubah menjadi 100% full screen stretch */
    .asri-container { 
        width: 100%; 
        max-width: 100% !important; 
        margin: 0; 
        display: flex; 
        flex-direction: column; 
        gap: 24px; 
        box-sizing: border-box;
    }
    
    .asri-header-row { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        flex-wrap: wrap; 
        gap: 20px; 
        width: 100%; 
        box-sizing: border-box; 
    }
    .header-text { flex: 1; min-width: 300px; }
    
    .page-title { font-size: 24px; font-weight: 800; color: #0e3820; margin: 0; letter-spacing: -0.3px; }
    .page-subtitle { margin: 6px 0 0 0; color: #64748b; font-size: 14px; font-weight: 400; }
    
    .filter-card { background: #ffffff; padding: 10px 16px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; gap: 10px; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .asri-select { border: 1px solid #e2e8f0; padding: 8px 36px 8px 14px; border-radius: 8px; background: #ffffff; font-size: 13px; color: #475569; font-weight: 500; outline: none; cursor: pointer; transition: 0.2s; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M12 14l-4-4h8z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 18px; }
    .asri-input-date { border: 1px solid #e2e8f0; padding: 7px 14px; border-radius: 8px; background: #ffffff; font-size: 13px; color: #475569; font-weight: 500; outline: none; transition: 0.2s; }
    .btn-clear { color: #64748b; display: flex; align-items: center; justify-content: center; padding: 8px; border-radius: 8px; background: #f1f5f9; text-decoration: none; transition: 0.2s; }
    .btn-clear:hover { background: #e2e8f0; color: #334155; }

    .asri-card { border-radius: 16px; padding: 28px; border: 1px solid #e2e8f0; background: #ffffff; box-sizing: border-box; width: 100%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01); }
    .info-panel-today { background: #0e3820; border: none; }
    .section-title { font-size: 16px; font-weight: 700; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .text-white { color: #ffffff; }
    .text-dark { color: #0e3820; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; font-size: 16px; }
    
    .empty-today-wrapper { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px 0; text-align: center; color: rgba(255, 255, 255, 0.35); gap: 8px; }
    .empty-today-wrapper i { font-size: 32px; }
    .empty-text { font-size: 14px; margin: 0; font-weight: 400; }
    .text-light { color: rgba(255, 255, 255, 0.75); }

    /* Mengubah grid hari ini agar adaptif melebar penuh jika monitornya luas */
    .today-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 14px; }
    .today-item-card { background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.08); padding: 14px 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; gap: 15px; color: white; transition: 0.2s; }
    .today-item-card:hover { background: rgba(255, 255, 255, 0.09); }
    .today-time { font-weight: 700; background: #ffffff; color: #0e3820; padding: 6px 12px; border-radius: 8px; font-size: 12px; display: flex; align-items: center; gap: 4px; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .today-body { flex: 1; }
    .today-body h4 { margin: 0; font-size: 15px; font-weight: 600; }
    .today-body p { margin: 4px 0 0 0; font-size: 13px; opacity: 0.75; display: flex; align-items: center; gap: 4px; }
    .btn-action-check { background: #22c55e; color: white; border: none; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: 0.2s; }
    .btn-action-check:hover { background: #16a34a; }

    .table-card { padding: 24px; }
    .table-responsive { width: 100%; overflow-x: auto; margin-top: 10px; }
    .asri-table { width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; }
    .asri-table th { padding: 14px 18px; color: #0e3820; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; background: #f0fdf4; border-bottom: 2px solid #e2e8f0; }
    .asri-table td { padding: 16px 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 13px; word-wrap: break-word; color: #475569; }
    .asri-table tbody tr:hover td { background-color: #f8fafc; }
    
    .user-info-cell { display: flex; flex-direction: column; gap: 2px; }
    .user-name { font-weight: 600; color: #0e3820; font-size: 14px; }
    .user-sub { font-size: 12px; color: #94a3b8; }
    .unit-badge { background: #f0fdf4; color: #16a34a; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-block; font-size: 12px; }
    .date-time-cell { display: flex; flex-direction: column; gap: 2px; }
    .date-text { color: #334155; font-weight: 600; font-size: 14px; }
    .time-text { font-size: 12px; color: #64748b; }
    
    .status-pill { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-dikonfirmasi { background: #dcfce7; color: #15803d; }
    .status-selesai { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .status-dibatalkan { background: #fee2e2; color: #b91c1c; }

    .actions-wrapper { display: flex; gap: 8px; align-items: center; justify-content: flex-start; }
    .btn-table { border: none; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; text-decoration: none; white-space: nowrap; }
    .btn-table-approve { background: #0e3820; color: white; }
    .btn-table-approve:hover { background: #144d2c; }
    .btn-table-cancel { background: #ffffff; color: #ef4444; border: 1px solid #fecaca; }
    .btn-table-cancel:hover { background: #fee2e2; }
    .btn-table-done { background: #e2e8f0; color: #475569; }
    .btn-table-done:hover { background: #cbd5e1; }
    .text-muted-status { font-size: 12px; color: #94a3b8; font-style: italic; }

    .asri-pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
    .toast-alert { position: fixed; top: 25px; right: 25px; background: white; padding: 14px 24px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px; z-index: 10000; font-size: 14px; }
    .alert-success { border-left: 4px solid #22c55e; }
    .alert-error { border-left: 4px solid #ef4444; }

    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(14, 56, 32, 0.2); backdrop-filter: blur(2px); z-index: 11000; align-items: center; justify-content: center; padding: 20px; }
    .modal-box { background: white; padding: 28px; border-radius: 16px; max-width: 460px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.06); box-sizing: border-box; }
    .modal-title { margin-top: 0; color: #b91c1c; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 6px; }
    .modal-desc { font-size: 13px; color: #64748b; line-height: 1.5; margin: 6px 0 16px 0; }
    .asri-textarea { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 13px; background: #fafafa; outline: none; resize: none; box-sizing: border-box; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
    .btn-modal-back { background: #f1f5f9; border: none; color: #475569; font-weight: 600; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; }
    .btn-modal-danger { background: #ef4444; border: none; color: white; font-weight: 600; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; }
</style>

<script>
    // TRIK JAVASCRIPT: Mengubah Teks "Dashboard" di Ujung Atas Secara Paksa & Aman
    document.addEventListener("DOMContentLoaded", function() {
        const elements = document.querySelectorAll('h1, h2, span, div, p');
        elements.forEach(el => {
            if (el.textContent.trim() === 'Dashboard' && !el.closest('.asri-wrapper')) {
                el.textContent = 'Kelola Jadwal Survei';
                el.style.fontFamily = "'Plus Jakarta Sans', sans-serif";
                el.style.fontWeight = "700";
                el.style.color = "#0e3820"; 
            }
        });
    });

    function openCancelModal(id) {
        const modal = document.getElementById('cancel-modal');
        const form = document.getElementById('cancel-form');
        form.action = `/admin/jadwal/${id}/batalkan`;
        modal.style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancel-modal').style.display = 'none';
    }

    setTimeout(() => {
        const alert = document.getElementById('asri-alert');
        if(alert) {
            alert.style.transition = '0.3s opacity';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }
    }, 4000);
</script>
@endsection