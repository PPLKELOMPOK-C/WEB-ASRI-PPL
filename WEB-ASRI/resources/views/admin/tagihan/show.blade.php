{{-- resources/views/admin/tagihan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tagihan #' . str_pad($tagihan->id, 6, '0', STR_PAD_LEFT))

@push('styles')
<style>
    /* ===== BACK BUTTON ===== */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: #fff;
        color: #0e3820;
        border: 1.5px solid #c9e8d5;
        border-radius: .65rem;
        padding: .45rem 1.1rem;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: all .2s;
    }
    .btn-back:hover {
        background: #0e3820;
        color: white;
        border-color: #0e3820;
        text-decoration: none;
    }

    /* ===== PAGE HEADER ===== */
    .page-header {
        background: linear-gradient(135deg, #0e3820 0%, #1a5c38 60%, #2a9d5c 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content: '';
        position: absolute;
        bottom: -50px; right: -30px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .invoice-number {
        font-size: .82rem;
        opacity: .75;
        letter-spacing: .07em;
        font-weight: 600;
        margin-bottom: .3rem;
    }

    /* ===== STATUS BADGE (large) ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .45rem 1.2rem;
        border-radius: 2rem;
        font-size: .85rem;
        font-weight: 700;
    }
    .status-pill .dot { width: 8px; height: 8px; border-radius: 50%; }
    .pill-belum    { background: #fff3cd; color: #856404; }
    .pill-belum    .dot { background: #ffc107; }
    .pill-menunggu { background: #cff4fc; color: #0c5460; }
    .pill-menunggu .dot { background: #0dcaf0; }
    .pill-lunas    { background: #d1f2e2; color: #0e5c30; }
    .pill-lunas    .dot { background: #2a9d5c; }
    .pill-terlambat{ background: #fde8e8; color: #8b1a1a; }
    .pill-terlambat .dot { background: #dc3545; }
    .pill-ditolak  { background: #f0e0e0; color: #6d2c2c; }
    .pill-ditolak  .dot { background: #c0392b; }

    /* ===== CARD SHARED ===== */
    .info-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e8f5ee;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .card-head {
        padding: .9rem 1.4rem;
        border-bottom: 1px solid #f0f7f2;
        display: flex;
        align-items: center;
        gap: .6rem;
        font-size: .88rem;
        font-weight: 700;
        color: #0e3820;
    }
    .card-head .icon {
        width: 28px; height: 28px;
        background: linear-gradient(135deg, #e8f5ee, #c9e8d5);
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem;
        color: #2a9d5c;
        flex-shrink: 0;
    }
    .card-body-pad { padding: 1.4rem; }

    /* ===== INFO ROW ===== */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: .65rem 0;
        border-bottom: 1px dashed #f0f7f2;
        gap: 1rem;
    }
    .info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .info-row:first-child { padding-top: 0; }
    .info-label {
        font-size: .82rem;
        color: #7a9e83;
        font-weight: 500;
        flex-shrink: 0;
        min-width: 130px;
    }
    .info-value {
        font-size: .88rem;
        color: #1a3d22;
        font-weight: 600;
        text-align: right;
    }
    .info-value.big {
        font-size: 1.1rem;
        color: #0e3820;
        font-weight: 800;
        font-family: 'Courier New', monospace;
    }

    /* ===== BUKTI BAYAR ===== */
    .bukti-box {
        border: 2px dashed #c9e8d5;
        border-radius: .85rem;
        padding: 1.25rem;
        text-align: center;
        background: #f8fdf9;
    }
    .bukti-box img {
        max-width: 100%;
        max-height: 320px;
        object-fit: contain;
        border-radius: .65rem;
        border: 1px solid #e0f0e7;
        cursor: zoom-in;
    }
    .bukti-empty {
        color: #a0bfa8;
        font-size: .88rem;
    }
    .bukti-empty i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }

    /* ===== CATATAN PENOLAKAN ===== */
    .rejection-box {
        background: #fde8e8;
        border: 1px solid #f5c6c6;
        border-left: 4px solid #dc3545;
        border-radius: .75rem;
        padding: 1rem 1.25rem;
        margin-top: 1rem;
    }
    .rejection-box p {
        font-size: .86rem;
        color: #8b1a1a;
        margin: 0;
    }

    /* ===== ACTION BUTTONS ===== */
    .btn-verif {
        background: linear-gradient(135deg, #2a9d5c, #1a7a45);
        color: white;
        border: none;
        border-radius: .75rem;
        padding: .65rem 1.5rem;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }
    .btn-verif:hover {
        background: linear-gradient(135deg, #0e3820, #1a5c38);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(14,56,32,.2);
    }
    .btn-tolak {
        background: #fff;
        color: #dc3545;
        border: 2px solid #f5c6c6;
        border-radius: .75rem;
        padding: .65rem 1.5rem;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }
    .btn-tolak:hover {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    .btn-download {
        background: #f4fbf6;
        color: #1a5c38;
        border: 1.5px solid #c9e8d5;
        border-radius: .65rem;
        padding: .5rem 1.1rem;
        font-size: .83rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: all .2s;
    }
    .btn-download:hover {
        background: #0e3820;
        color: white;
        border-color: #0e3820;
        text-decoration: none;
    }

    /* ===== TIMELINE ===== */
    .timeline { padding: 0; list-style: none; }
    .timeline li {
        display: flex;
        gap: 1rem;
        padding-bottom: 1.1rem;
        position: relative;
    }
    .timeline li:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 14px; top: 28px;
        width: 2px;
        height: calc(100% - 10px);
        background: linear-gradient(to bottom, #c9e8d5, transparent);
    }
    .tl-dot {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e8f5ee, #c9e8d5);
        border: 2px solid #2a9d5c;
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem;
        color: #2a9d5c;
        flex-shrink: 0;
        margin-top: .1rem;
    }
    .tl-dot.active {
        background: linear-gradient(135deg, #2a9d5c, #0e3820);
        border-color: #0e3820;
        color: white;
    }
    .tl-content .tl-title {
        font-size: .86rem;
        font-weight: 700;
        color: #0e3820;
    }
    .tl-content .tl-time {
        font-size: .76rem;
        color: #7a9e83;
        margin-top: .1rem;
    }

    /* ===== MODAL TOLAK ===== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: slideUp .25s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .modal-box h5 {
        font-weight: 800;
        color: #dc3545;
        margin-bottom: .5rem;
    }
    .modal-box textarea {
        width: 100%;
        border: 1.5px solid #e0e0e0;
        border-radius: .65rem;
        padding: .75rem;
        font-size: .87rem;
        resize: vertical;
        min-height: 100px;
        outline: none;
        margin-top: .75rem;
        transition: border-color .2s;
    }
    .modal-box textarea:focus { border-color: #dc3545; }

    /* ===== IMAGE LIGHTBOX ===== */
    .lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.9);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        cursor: zoom-out;
    }
    .lightbox.active { display: flex; }
    .lightbox img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: .5rem;
    }
</style>
@endpush

@section('content')

{{-- Back --}}
<a href="{{ route('admin.tagihan.index') }}" class="btn-back">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Tagihan
</a>

{{-- ===== PAGE HEADER ===== --}}
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="invoice-number">INVOICE · #{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</div>
            <h3 style="font-family:'Playfair Display',serif; font-weight:700; margin-bottom:.5rem;">
                Detail Tagihan Bulanan
            </h3>
            <div style="opacity:.8; font-size:.88rem;">
                {{ \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}
                &nbsp;·&nbsp;
                Unit {{ $tagihan->unit?->kode_unit ?? '-' }}
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0" style="position:relative; z-index:1;">
            @php
                $st = $tagihan->status;
$isOverdue = $st === 'belum_bayar' && $tagihan->tanggal_jatuh_tempo && now()->gt($tagihan->tanggal_jatuh_tempo);                if ($isOverdue) $st = 'terlambat';
            @endphp
            <span class="status-pill pill-{{ $st }}">
                <span class="dot"></span>
                {{ ['belum_bayar'=>'Belum Bayar','terlambat'=>'Terlambat','menunggu'=>'Menunggu Verifikasi','lunas'=>'Lunas','ditolak'=>'Bukti Ditolak'][$st] ?? ucfirst($st) }}
            </span>
        </div>
    </div>
</div>

<div class="row">

    {{-- ===== KOLOM KIRI ===== --}}
    <div class="col-lg-7">

        {{-- Info Tagihan --}}
        <div class="info-card">
            <div class="card-head">
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                Informasi Tagihan
            </div>
            <div class="card-body-pad">
                <div class="info-row">
                    <span class="info-label">Periode</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis Tagihan</span>
                    <span class="info-value">{{ $tagihan->jenis_tagihan_label ?? 'Sewa Bulanan' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nominal</span>
                    <span class="info-value big">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jatuh Tempo</span>
                    <span class="info-value {{ $isOverdue ? 'text-danger' : '' }}">
                        @if($isOverdue) <i class="fas fa-exclamation-triangle me-1 text-danger"></i> @endif
                        {{ $tagihan->tanggal_jatuh_tempo->format('d M Y') }}
                        @if($isOverdue)
                            <br><small class="text-danger fw-normal">
                                (Terlambat {{ now()->diffInDays($tagihan->tanggal_jatuh_tempo) }} hari)
                            </small>
                        @endif
                    </span>
                </div>
                @if($tagihan->keterangan)
                <div class="info-row">
                    <span class="info-label">Keterangan</span>
                    <span class="info-value fw-normal" style="color:#4a7055;">{{ $tagihan->keterangan }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Dibuat</span>
                    <span class="info-value fw-normal" style="color:#7a9e83;">
                        {{ $tagihan->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Info Penghuni --}}
        <div class="info-card">
            <div class="card-head">
                <div class="icon"><i class="fas fa-user"></i></div>
                Data Penghuni
            </div>
            <div class="card-body-pad">
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $tagihan->user?->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NIK</span>
                    <span class="info-value fw-normal">{{ $tagihan->user?->nik ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. HP</span>
                    <span class="info-value fw-normal">{{ $tagihan->user?->no_hp ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Unit</span>
                    <span class="info-value">
                        {{ $tagihan->unit?->kode_unit ?? '-' }}
                        <span style="font-weight:400; color:#7a9e83; font-size:.82rem;">
                            · {{ $tagihan->unit?->blok ?? '' }} Lt.{{ $tagihan->unit?->lantai ?? '' }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== KOLOM KANAN ===== --}}
    <div class="col-lg-5">

        {{-- Bukti Pembayaran --}}
        <div class="info-card">
            <div class="card-head">
                <div class="icon"><i class="fas fa-receipt"></i></div>
                Bukti Pembayaran
                @if($tagihan->bukti_bayar)
                    <a href="{{ Storage::url($tagihan->bukti_bayar) }}"
                       download class="btn-download ms-auto">
                        <i class="fas fa-download"></i> Unduh
                    </a>
                @endif
            </div>
            <div class="card-body-pad">
                @if($tagihan->bukti_bayar)
                    <div class="bukti-box">
                        <img src="{{ Storage::url($tagihan->bukti_bayar) }}"
                             alt="Bukti Bayar"
                             onclick="openLightbox(this.src)">
                        <div style="font-size:.78rem; color:#7a9e83; margin-top:.75rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Diunggah {{ $tagihan->bukti_bayar_at ? \Carbon\Carbon::parse($tagihan->bukti_bayar_at)->locale('id')->diffForHumans() : '-' }}
                        </div>
                    </div>

                    @if($tagihan->status === 'ditolak' && $tagihan->catatan_penolakan)
                        <div class="rejection-box">
                            <div style="font-size:.8rem; font-weight:700; color:#dc3545; margin-bottom:.35rem;">
                                <i class="fas fa-times-circle me-1"></i> Alasan Penolakan
                            </div>
                            <p>{{ $tagihan->catatan_penolakan }}</p>
                        </div>
                    @endif

                    {{-- AKSI ADMIN --}}
                    @if($tagihan->status === 'menunggu')
                        <div class="d-flex gap-2 mt-3">
                            <form action="{{ route('admin.tagihan.verifikasi', $tagihan->id) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn-verif w-100"
                                        onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')">
                                    <i class="fas fa-check-circle"></i> Konfirmasi Lunas
                                </button>
                            </form>
                            <button type="button" class="btn-tolak"
                                    onclick="document.getElementById('modalTolak').classList.add('active')">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    @endif

                @else
                    <div class="bukti-box">
                        <div class="bukti-empty">
                            <i class="fas fa-image"></i>
                            Belum ada bukti pembayaran<br>yang diunggah oleh penghuni.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Status --}}
        <div class="info-card">
            <div class="card-head">
                <div class="icon"><i class="fas fa-history"></i></div>
                Riwayat Status
            </div>
            <div class="card-body-pad">
                <ul class="timeline">
                    <li>
                        <div class="tl-dot active"><i class="fas fa-plus"></i></div>
                        <div class="tl-content">
                            <div class="tl-title">Tagihan Dibuat</div>
                            <div class="tl-time">{{ $tagihan->created_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}</div>
                        </div>
                    </li>
                    @if($tagihan->bukti_bayar_at)
                    <li>
                        <div class="tl-dot active"><i class="fas fa-upload"></i></div>
                        <div class="tl-content">
                            <div class="tl-title">Bukti Diunggah Penghuni</div>
                            <div class="tl-time">{{ \Carbon\Carbon::parse($tagihan->bukti_bayar_at)->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}</div>
                        </div>
                    </li>
                    @endif
                    @if($tagihan->status === 'lunas')
                    <li>
                        <div class="tl-dot active" style="background:linear-gradient(135deg,#2a9d5c,#0e3820); border-color:#0e3820;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="tl-content">
                            <div class="tl-title" style="color:#2a9d5c;">Pembayaran Dikonfirmasi ✓</div>
                            <div class="tl-time">{{ $tagihan->updated_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}</div>
                        </div>
                    </li>
                    @endif
                    @if($tagihan->status === 'ditolak')
                    <li>
                        <div class="tl-dot" style="background:#fde8e8; border-color:#dc3545; color:#dc3545;">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="tl-content">
                            <div class="tl-title" style="color:#dc3545;">Bukti Ditolak</div>
                            <div class="tl-time">{{ $tagihan->updated_at->locale('id')->isoFormat('D MMM YYYY · HH:mm') }}</div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

    </div>
</div>

{{-- ===== MODAL TOLAK ===== --}}
<div class="modal-overlay" id="modalTolak"
     onclick="if(event.target===this) this.classList.remove('active')">
    <div class="modal-box">
        <h5><i class="fas fa-times-circle me-2"></i>Tolak Bukti Pembayaran</h5>
        <p style="font-size:.86rem; color:#666; margin:0;">
            Berikan alasan penolakan. Penghuni akan mendapat notifikasi dan perlu mengunggah ulang bukti.
        </p>
        <form action="{{ route('admin.tagihan.tolak', $tagihan->id) }}" method="POST">
            @csrf
            <textarea name="catatan_penolakan"
                      placeholder="Contoh: Bukti tidak terbaca, nominal tidak sesuai, dll."
                      required></textarea>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn-tolak flex-fill" style="justify-content:center;">
                    <i class="fas fa-times-circle"></i> Konfirmasi Tolak
                </button>
                <button type="button" class="btn-back mb-0 flex-fill" style="justify-content:center;"
                        onclick="document.getElementById('modalTolak').classList.remove('active')">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== LIGHTBOX ===== --}}
<div class="lightbox" id="lightbox" onclick="this.classList.remove('active')">
    <img id="lightboxImg" src="" alt="Bukti Bayar">
</div>

@endsection

@push('scripts')
<script>
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('active');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('lightbox')?.classList.remove('active');
        document.getElementById('modalTolak')?.classList.remove('active');
    }
});
</script>
@endpush