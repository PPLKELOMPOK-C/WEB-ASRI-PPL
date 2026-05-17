@extends('layouts.app')

@section('title', 'Atur Jadwal Survei')

@section('content')
{{-- Load Font & Icon Khas ASRI (Menggunakan CDN Terupdate & Stabil) --}}
<link href="https://unpkg.com/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap" rel="stylesheet">

<div class="asri-wrapper">
    <div class="asri-container">
        
        {{-- Breadcrumb / Back Button --}}
        <div class="asri-navigation-row">
            <a href="{{ route('calon.survei.index') }}" class="asri-back-btn">
                <i class="ri-arrow-left-line"></i> Kembali ke Katalog Unit
            </a>
        </div>

        {{-- Main Layout Grid: Melebar Proporsional --}}
        <div class="asri-grid-layout">
            
            {{-- SISI KIRI: KARTU INFORMASI UNIT --}}
            <div class="asri-card unit-summary-card">
                <div class="image-container-wrapper">
                    @if($unit->gambar)
                        <img src="{{ Storage::url($unit->gambar) }}" class="unit-main-img" alt="Gambar {{ $unit->nama_unit }}">
                    @else
                        <div class="unit-img-placeholder">
                            <i class="ri-building-2-line"></i>
                        </div>
                    @endif
                    <span class="region-badge"><i class="ri-map-pin-2-fill"></i> {{ $unit->wilayah ?? 'Jakarta Selatan' }}</span>
                </div>

                <div class="unit-detail-body">
                    <h1 class="unit-title-text">{{ $unit->nama_unit }}</h1>
                    <p class="unit-location-sub">{{ $unit->gedung }} · Blok {{ $unit->blok }}</p>
                    
                    <div class="specs-grid">
                        <div class="spec-item">
                            <i class="ri-ruler-2-line"></i>
                            <div>
                                <span class="spec-label">Luas Unit</span>
                                <span class="spec-value">{{ $unit->luas_m2 }} m²</span>
                            </div>
                        </div>
                        <div class="spec-item">
                            <i class="ri-money-dollar-circle-line"></i>
                            <div>
                                <span class="spec-label">Harga Sewa</span>
                                <span class="spec-value text-green">Rp {{ number_format($unit->harga_sewa, 0, ',', '.') }}<small>/bln</small></span>
                            </div>
                        </div>
                    </div>

                    <div class="operational-notice-box">
                        <div class="notice-header">
                            <i class="ri-time-line"></i>
                            <span>Catatan Operasional Survei:</span>
                        </div>
                        <ul class="notice-list">
                            <li>Pelayanan survei terjadwal dibuka <strong>Senin s/d Sabtu</strong>.</li>
                            <li>Waktu kunjungan lapangan tersedia pukul <strong>08:00 - 15:00 WIB</strong>.</li>
                            <li>Calon penghuni diwajibkan datang tepat waktu sesuai konfirmasi.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- SISI KANAN: FORM PENENTUAN JADWAL --}}
            <div class="asri-card form-schedule-card">
                <div class="form-header-zone">
                    <h2 class="form-title"><i class="ri-calendar-todo-line"></i> Tentukan Waktu Kunjungan</h2>
                    <p class="form-subtitle">Pilih tanggal dan jam senggang Anda. Sistem akan meneruskan jadwal ini ke Admin pengelola untuk disetujui.</p>
                </div>

                <form action="{{ route('calon.survei.simpan') }}" method="POST" class="asri-form-body">
                    @csrf
                    <input type="hidden" name="unit_id" value="{{ $unit->id }}">

                    <div class="form-group-row">
                        {{-- Input Tanggal --}}
                        <div class="input-block">
                            <label class="asri-label">Pilih Tanggal Survei</label>
                            <div class="input-icon-wrapper">
                                <i class="ri-calendar-line field-icon"></i>
                                <input type="date" name="tanggal" class="asri-input @error('tanggal') is-invalid @enderror" 
                                       min="{{ date('Y-m-d') }}" value="{{ old('tanggal') }}" required>
                            </div>
                            @error('tanggal') <span class="error-feedback">{{ $message }}</span> @enderror
                        </div>

                        {{-- Input Jam --}}
                        <div class="input-block">
                            <label class="asri-label">Pilih Jam Kedatangan</label>
                            <div class="input-icon-wrapper">
                                <i class="ri-time-line field-icon"></i>
                                <input type="time" name="jam" class="asri-input @error('jam') is-invalid @enderror" 
                                       value="{{ old('jam') }}" required>
                            </div>
                            <span class="field-help-text">Rentang operasional: 08:00 s/d 15:00 WIB</span>
                            @error('jam') <span class="error-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-action-zone">
                        <button type="submit" class="asri-btn-submit">
                            <i class="ri-checkbox-circle-line"></i> Ajukan Jadwal Survei Sekarang
                        </button>
                        <div class="info-footer-text">
                            <i class="ri-information-fill"></i> Sifat pengajuan ini adalah <em>booking</em> sementara. Status jadwal dapat Anda pantau secara berkala pada menu <strong>Jadwal Survei</strong>.
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
    /* ==========================================================================
       ASRI THEME FULL WIDTH CORE LAYOUT
       ========================================================================== */
    body, html { overflow-x: hidden; }
    main, .content-wrapper, .container { max-width: 100% !important; width: 100% !important; padding-left: 0 !important; padding-right: 0 !important; }

    .asri-wrapper { 
        background-color: #FDFDFB; 
        min-height: 100vh; 
        padding: 30px 40px; 
        width: 100%;
        box-sizing: border-box;
    }
    
    /* FIX UTAMA: Mencegah teks biasa merusak font-family milik icon (<i>) */
    .asri-wrapper *:not(i) { 
        font-family: 'Plus Jakarta Sans', sans-serif !important; 
        box-sizing: border-box; 
    }
    
    .asri-container { width: 100%; max-width: 100% !important; margin: 0; display: flex; flex-direction: column; gap: 20px; }
    
    /* Navigation Link */
    .asri-back-btn { 
        text-decoration: none; 
        color: #0e3820; 
        font-weight: 700; 
        font-size: 14px; 
        display: inline-flex; 
        align-items: center; 
        gap: 8px;
        transition: transform 0.2s;
    }
    .asri-back-btn:hover { transform: translateX(-4px); color: #165231; }

    /* Layout Grid Samping-Sampingan Melebar */
    .asri-grid-layout { 
        display: grid; 
        grid-template-columns: 1.2fr 1.8fr; 
        gap: 30px; 
        width: 100%; 
    }

    @media (max-width: 992px) {
        .asri-grid-layout { grid-template-columns: 1fr; }
    }

    /* Base Component Card */
    .asri-card { 
        border-radius: 16px; 
        border: 1px solid #e2e8f0; 
        background: #ffffff; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
    }

    /* ==========================================================================
       SISI KRI: DETAIL UNIT
       ========================================================================== */
    .unit-summary-card { padding: 0; overflow: hidden; background: #fafbfa; }
    
    .image-container-wrapper { width: 100%; height: 240px; position: relative; background: #e2e8f0; }
    .unit-main-img { width: 100%; height: 100%; object-fit: cover; }
    .unit-img-placeholder { width: 100%; height: 100%; background: #dcfce7; display: flex; align-items: center; justify-content: center; color: #15803d; font-size: 54px; }
    
    .region-badge { 
        position: absolute; 
        top: 16px; 
        left: 16px; 
        background: rgba(14, 56, 32, 0.9); 
        color: #ffffff; 
        padding: 6px 14px; 
        border-radius: 30px; 
        font-size: 11px; 
        font-weight: 700; 
        letter-spacing: 0.3px;
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .unit-detail-body { padding: 26px; flex-grow: 1; display: flex; flex-direction: column; gap: 20px; }
    .unit-title-text { font-size: 22px; font-weight: 800; color: #0e3820; margin: 0; letter-spacing: -0.5px; }
    .unit-location-sub { font-size: 14px; color: #64748b; margin: -14px 0 0 0; font-weight: 500; }

    .specs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 16px 0; }
    .spec-item { display: flex; align-items: center; gap: 12px; }
    .spec-item i { font-size: 22px; color: #15803d; background: #f0fdf4; padding: 8px; border-radius: 10px; display: inline-flex; justify-content: center; align-items: center; }
    .spec-label { display: block; font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
    .spec-value { font-size: 14px; font-weight: 700; color: #1e293b; }
    .text-green { color: #15803d; }
    .spec-value small { font-size: 11px; font-weight: 500; color: #64748b; margin-left: 2px; }

    .operational-notice-box { background: #f1f5f9; border-left: 4px solid #475569; padding: 16px; border-radius: 0 12px 12px 0; }
    .notice-header { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 8px; }
    .notice-header i { color: #475569; font-size: 16px; }
    .notice-list { margin: 0; padding-left: 18px; font-size: 12px; color: #475569; display: flex; flex-direction: column; gap: 6px; line-height: 1.5; }

    /* ==========================================================================
       SISI KANAN: FORM CARD
       ========================================================================== */
    .form-schedule-card { padding: 35px; justify-content: center; }
    .form-header-zone { border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px; }
    .form-title { font-size: 18px; font-weight: 800; color: #0e3820; margin: 0; display: flex; align-items: center; gap: 10px; }
    .form-subtitle { margin: 8px 0 0 0; color: #64748b; font-size: 13px; line-height: 1.5; font-weight: 400; }

    .asri-form-body { display: flex; flex-direction: column; gap: 24px; }
    .form-group-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    @media (max-width: 576px) {
        .form-group-row { grid-template-columns: 1fr; }
    }

    .input-block { display: flex; flex-direction: column; gap: 8px; }
    .asri-label { font-size: 13px; font-weight: 700; color: #0e3820; letter-spacing: -0.1px; }
    
    .input-icon-wrapper { position: relative; width: 100%; }
    .field-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px; pointer-events: none; display: inline-flex; align-items: center; }
    
    .asri-input { 
        width: 100%; 
        padding: 14px 14px 14px 44px; 
        border-radius: 10px; 
        border: 1px solid #cbd5e1; 
        background: #ffffff;
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
        transition: all 0.2s;
    }
    .asri-input:focus { outline: none; border-color: #0e3820; box-shadow: 0 0 0 3px rgba(14, 56, 32, 0.1); background: #fafbfa; }
    .asri-input.is-invalid { border-color: #ef4444; background: #fef2f2; }
    
    .field-help-text { font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px; display: block; }
    .error-feedback { font-size: 12px; color: #ef4444; font-weight: 600; margin-top: 4px; }

    /* Button Submit */
    .form-action-zone { display: flex; flex-direction: column; gap: 16px; margin-top: 10px; }
    .asri-btn-submit { 
        width: 100%; 
        background: #0e3820; 
        color: #ffffff; 
        padding: 16px; 
        border: none; 
        border-radius: 12px; 
        font-weight: 700; 
        font-size: 15px; 
        cursor: pointer; 
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(14, 56, 32, 0.15);
        transition: all 0.2s ease-in-out; 
    }
    .asri-btn-submit:hover { background: #165231; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(14, 56, 32, 0.25); }
    .asri-btn-submit:active { transform: translateY(0); }

    .info-footer-text { 
        background: #f0fdf4; 
        border: 1px solid #dcfce7; 
        padding: 12px 16px; 
        border-radius: 8px; 
        font-size: 11px; 
        color: #166534; 
        line-height: 1.5; 
        display: flex; 
        gap: 8px; 
        align-items: flex-start; 
    }
    .info-footer-text i { font-size: 14px; margin-top: 1px; color: #15803d; display: inline-flex; }
</style>
@endsection