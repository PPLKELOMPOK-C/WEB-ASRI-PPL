@extends('layouts.public')
@section('title', 'Cari Unit Rusun')

@section('content')
{{-- Load Font & Icon Khas ASRI --}}
<link href="https://unpkg.com/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap" rel="stylesheet">

<div class="asri-public-wrapper">
    
    {{-- ZONE 1: HERO & FILTER PENCARIAN (FULL WIDTH BACKGROUND) --}}
    <div class="asri-hero-filter-zone">
        <div class="asri-fluid-container">
            <h1 class="asri-page-title"><i class="ri-search-eye-line"></i> Jelajahi & Cari Unit Rusun</h1>
            <p class="asri-page-subtitle">Temukan hunian vertikal yang nyaman, strategis, dan sesuai dengan anggaran bulanan Anda.</p>
            
            <form method="GET" class="asri-filter-form">
                <div class="filter-group">
                    <label class="filter-label">Kata Kunci</label>
                    <div class="filter-input-wrapper">
                        <i class="ri-search-line filter-icon"></i>
                        <input type="text" name="search" placeholder="Cari nama gedung, blok, alamat..." value="{{ request('search') }}" class="filter-control">
                    </div>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Wilayah</label>
                    <div class="filter-input-wrapper">
                        <i class="ri-map-pin-5-line filter-icon"></i>
                        <select name="wilayah" class="filter-control select-control">
                            <option value="">Semua Wilayah</option>
                            @foreach($wilayahs as $w)
                                <option value="{{ $w }}" {{ request('wilayah') == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Harga Maksimal (Rp)</label>
                    <div class="filter-input-wrapper">
                        <i class="ri-money-dollar-circle-line filter-icon"></i>
                        <input type="number" name="harga_max" placeholder="Contoh: 1500000" value="{{ request('harga_max') }}" class="filter-control">
                    </div>
                </div>
                
                <div class="filter-group action-group">
                    <label class="checkbox-label-wrapper">
                        <input type="checkbox" name="tersedia" value="1" {{ request('tersedia') ? 'checked' : '' }} class="asri-checkbox">
                        <span>Hanya Unit Tersedia</span>
                    </label>
                    <button type="submit" class="asri-btn-search">
                        <i class="ri-search-2-line"></i> Cari Unit
                    </button>
                </div>
            </form>
            
            @if(request()->anyFilled(['search','wilayah','harga_max','tersedia']))
                <div class="filter-meta-info">
                    <a href="{{ route('public.units') }}" class="asri-reset-link">
                        <i class="ri-refresh-line"></i> Bersihkan Semua Filter
                    </a>
                    <span class="results-count-badge">
                        <i class="ri-building-line"></i> Terfilter: {{ $units->total() }} Unit Hunian
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- ZONE 2: KATALOG GRID DAFTAR UNIT --}}
    <div class="asri-catalog-grid-zone">
        <div class="asri-fluid-container">
            
            @if($units->isEmpty())
                <div class="asri-empty-state-card">
                    <div class="empty-icon-circle">
                        <i class="ri-search-line"></i>
                    </div>
                    <h3 class="empty-title">Unit Tidak Ditemukan</h3>
                    <p class="empty-subtitle">Kombinasi filter atau kata kunci pencarian Anda tidak cocok dengan unit hunian manapun saat ini.</p>
                    <a href="{{ route('public.units') }}" class="asri-btn-reset-large">
                        <i class="ri-restart-line"></i> Reset Pencarian Katalog
                    </a>
                </div>
            @else
                {{-- Grid responsif melebar maksimal mengikuti layar --}}
                <div class="asri-cards-responsive-grid">
                    @foreach($units as $unit)
                        <div class="asri-catalog-card {{ $unit->status === 'tersedia' ? 'border-highlight' : '' }}">
                            
                            {{-- Foto Unit --}}
                            <div class="card-image-header">
                                @if($unit->gambar)
                                    <img src="{{ Storage::url($unit->gambar) }}" class="card-main-img" alt="{{ $unit->nama_unit }}">
                                @else
                                    <div class="card-img-placeholder">
                                        <i class="ri-building-4-line"></i>
                                    </div>
                                @endif
                                
                                <div class="status-badge-container">
                                    @if($unit->status === 'tersedia')
                                        <span class="badge badge-tersedia"><i class="ri-checkbox-circle-fill"></i> Tersedia</span>
                                    @elseif($unit->status === 'dihuni')
                                        <span class="badge badge-dihuni"><i class="ri-group-fill"></i> Tersewa</span>
                                    @else
                                        <span class="badge badge-maintenance"><i class="ri-tools-fill"></i> Pemeliharaan</span>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Konten Detail Unit --}}
                            <div class="card-content-body">
                                <span class="card-region-text"><i class="ri-map-pin-2-line"></i> {{ $unit->wilayah }}</span>
                                <h3 class="card-title-text">{{ $unit->nama_unit }}</h3>
                                <p class="card-sub-location">{{ $unit->gedung }}{{ $unit->luas_m2 ? ' · Luas '.$unit->luas_m2.' m²' : '' }}</p>
                                
                                <div class="card-footer-pricing-row">
                                    <div class="price-box">
                                        <span class="price-amount">Rp {{ number_format($unit->harga_sewa, 0, ',', '.') }}</span>
                                        <span class="price-period">/ bulan</span>
                                    </div>
                                    <a href="{{ route('public.unit.detail', $unit) }}" class="asri-btn-detail">
                                        Lihat Detail <i class="ri-arrow-right-s-line"></i>
                                    </a>
                                </div>
                            </div>
                            
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links khas Laravel --}}
                <div class="asri-pagination-container">
                    {{ $units->withQueryString()->links() }}
                </div>
            @endif
            
        </div>
    </div>

</div>

<style>
    /* ==========================================================================
       ASRI THEME PUBLIC CATALOG FULL-WIDTH GRID SYSTEM
       ========================================================================== */
    body, html { overflow-x: hidden; margin: 0; padding: 0; }
    /* Memaksa wrapper layout utama framework untuk melebar */
    main, .content-wrapper, .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }

    .asri-public-wrapper {
        background-color: #FDFDFB;
        min-height: 100vh;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    /* FIX IKON KOTAK: Mencegah font-family melibas elemen icon (<i>) */
    .asri-public-wrapper *:not(i) {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        box-sizing: border-box;
    }

    /* Container Fleksibel Melebar Penuh Mengikuti Lebar Viewport Samping Layar */
    .asri-fluid-container {
        width: 100%;
        max-width: 100% !important;
        padding-left: 5%;
        padding-right: 5%;
    }

    /* ==========================================================================
       ZONE 1: HERO & ADVANCED FORM FILTER CARD
       ========================================================================== */
    .asri-hero-filter-zone {
        background: #0e3820; /* Hijau Tua Khas ASRI */
        padding: 50px 0 60px 0;
        border-bottom: 1px solid #165231;
    }
    .asri-page-title {
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .asri-page-subtitle {
        margin: 8px 0 32px 0;
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        max-width: 700px;
    }

    /* Form Grid Filter Mengisi Seluruh Baris Lebar */
    .asri-filter-form {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1.2fr;
        gap: 16px;
        align-items: flex-end;
        background: rgba(255, 255, 255, 0.06);
        padding: 24px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
    }

    @media (max-width: 992px) {
        .asri-filter-form { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 576px) {
        .asri-filter-form { grid-template-columns: 1fr; }
    }

    .filter-group { display: flex; flex-direction: column; gap: 8px; }
    .filter-label { font-size: 12px; color: rgba(255, 255, 255, 0.8); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .filter-input-wrapper { position: relative; width: 100%; }
    .filter-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px; pointer-events: none; display: inline-flex; }
    
    .filter-control {
        width: 100%;
        padding: 14px 14px 14px 44px;
        border-radius: 10px;
        border: 1px solid transparent;
        background: #ffffff;
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
        outline: none;
        transition: all 0.2s;
    }
    .filter-control:focus { border-color: #4ade80; box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2); }
    .select-control { appearance: none; cursor: pointer; }

    /* Action box group untuk checkbox & tombol cari */
    .action-group { display: flex; flex-direction: row; gap: 16px; align-items: center; justify-content: space-between; }
    .checkbox-label-wrapper { display: flex; align-items: center; gap: 8px; color: #ffffff; font-size: 13px; font-weight: 600; cursor: pointer; user-select: none; margin-bottom: 12px; }
    .asri-checkbox { width: 18px; height: 18px; accent-color: #4ade80; cursor: pointer; border-radius: 4px; }
    
    .asri-btn-search {
        background: #15803d;
        color: #ffffff;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(21, 128, 61, 0.2);
        transition: all 0.2s;
        flex-grow: 1;
        justify-content: center;
    }
    .asri-btn-search:hover { background: #166534; transform: translateY(-1px); }

    .filter-meta-info { display: flex; align-items: center; justify-content: space-between; margin-top: 16px; padding: 0 4px; }
    .asri-reset-link { color: rgba(255, 255, 255, 0.6); font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .asri-reset-link:hover { color: #ffffff; }
    .results-count-badge { font-size: 13px; color: rgba(255, 255, 255, 0.7); background: rgba(255, 255, 255, 0.1); padding: 4px 12px; border-radius: 30px; display: inline-flex; align-items: center; gap: 6px; }

    /* ==========================================================================
       ZONE 2: CATALOG DAFTAR CARDS (MELEBAR LEGA)
       ========================================================================== */
    .asri-catalog-grid-zone { padding: 48px 0; flex-grow: 1; }

    /* Responsive Grid otomatis nambah kolom jika layar ekstra lebar (Up to 4 kolom) */
    .asri-cards-responsive-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 28px;
        width: 100%;
    }

    @media (max-width: 1400px) {
        .asri-cards-responsive-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 992px) {
        .asri-cards-responsive-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .asri-cards-responsive-grid { grid-template-columns: 1fr; }
    }

    /* Card Item Style Khas ASRI */
    .asri-catalog-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease-in-out;
    }
    .asri-catalog-card:hover { transform: translateY(-6px); box-shadow: 0 12px 24px -4px rgba(14, 56, 32, 0.08); }
    .asri-catalog-card.border-highlight { border-color: #bbf7d0; }

    .card-image-header { height: 200px; background: #f1f5f9; position: relative; overflow: hidden; width: 100%; }
    .card-main-img { width: 100%; height: 100%; object-fit: cover; }
    .card-img-placeholder { display: flex; align-items: center; justify-content: center; height: 100%; background: #e8f5ed; color: #15803d; font-size: 48px; }
    
    .status-badge-container { position: absolute; top: 12px; right: 12px; z-index: 2; }
    .badge { padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .badge-tersedia { background: #dcfce7; color: #15803d; }
    .badge-dihuni { background: #dbeafe; color: #1e40af; }
    .badge-maintenance { background: #fef9c3; color: #854d0e; }

    /* Detail content body di dalam card */
    .card-content-body { padding: 22px; display: flex; flex-direction: column; flex-grow: 1; }
    .card-region-text { font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: inline-flex; align-items: center; gap: 4px; }
    .card-title-text { font-size: 16px; font-weight: 800; color: #0e3820; margin: 0 0 4px 0; line-height: 1.4; }
    .card-sub-location { font-size: 13px; color: #64748b; margin: 0 0 20px 0; font-weight: 500; }

    /* Bagian harga & aksi button di paling bawah card */
    .card-footer-pricing-row { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: auto; }
    .price-box { display: flex; flex-direction: column; }
    .price-amount { font-size: 18px; font-weight: 800; color: #0e3820; line-height: 1; }
    .price-period { font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px; }
    
    .asri-btn-detail {
        background: #0e3820;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: background 0.2s;
    }
    .asri-btn-detail:hover { background: #165231; }

    /* ==========================================================================
       EMPTY STATE CARD & PAGINATION
       ========================================================================== */
    .asri-empty-state-card { text-align: center; padding: 64px 32px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; max-width: 500px; margin: 40px auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01); }
    .empty-icon-circle { width: 72px; height: 72px; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #15803d; font-size: 32px; }
    .empty-title { font-size: 18px; font-weight: 800; color: #0e3820; margin: 0 0 6px 0; }
    .empty-subtitle { font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; }
    
    .asri-btn-reset-large { background: #0e3820; color: white; font-size: 13px; font-weight: 700; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .asri-btn-reset-large:hover { background: #165231; }

    .asri-pagination-container { margin-top: 40px; display: flex; justify-content: center; width: 100%; }
</style>
@endsection