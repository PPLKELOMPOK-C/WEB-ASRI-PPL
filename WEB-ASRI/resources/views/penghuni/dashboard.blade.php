@extends('layouts.app')
@section('title','Dashboard Penghuni')
@section('page-title','Dashboard Saya')

@section('content')

{{-- Info Hunian --}}
@if($kontrakAktif)
<div style="background:linear-gradient(135deg,var(--green-800),var(--green-600));border-radius:14px;padding:24px;margin-bottom:24px;color:white">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px">
        <div>
            <div style="font-size:12px;font-weight:600;opacity:0.7;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Unit Anda</div>
            <div style="font-size:24px;font-weight:700">{{ $kontrakAktif->unit->nama_unit }}</div>
            <div style="font-size:14px;opacity:0.8;margin-top:3px">{{ $kontrakAktif->unit->gedung }} · {{ $kontrakAktif->unit->wilayah }}</div>
            <div style="margin-top:14px;display:flex;gap:20px;flex-wrap:wrap">
                <div><div style="font-size:11px;opacity:0.65;font-weight:600">MULAI KONTRAK</div><div style="font-size:14px;font-weight:600;margin-top:2px">{{ $kontrakAktif->tanggal_mulai->format('d M Y') }}</div></div>
                <div><div style="font-size:11px;opacity:0.65;font-weight:600">SELESAI KONTRAK</div><div style="font-size:14px;font-weight:600;margin-top:2px">{{ $kontrakAktif->tanggal_selesai->format('d M Y') }}</div></div>
                <div><div style="font-size:11px;opacity:0.65;font-weight:600">HARGA/BULAN</div><div style="font-size:14px;font-weight:600;margin-top:2px">Rp {{ number_format($kontrakAktif->harga_per_bulan,0,',','.') }}</div></div>
            </div>
        </div>
        @if(isset($infoKontrak))
        <div style="text-align:center;background:rgba(255,255,255,0.15);border-radius:10px;padding:14px 20px;min-width:140px">
            <div style="font-size:32px;font-weight:700">{{ $infoKontrak['sisa_hari'] }}</div>
            <div style="font-size:12px;opacity:0.8">hari tersisa</div>
            <div style="margin-top:10px;background:rgba(255,255,255,0.25);border-radius:6px;height:6px">
                <div style="width:{{ $infoKontrak['progress'] }}%;background:rgba(255,255,255,0.85);border-radius:6px;height:6px"></div>
            </div>
            <div style="font-size:11px;opacity:0.7;margin-top:4px">{{ $infoKontrak['progress'] }}% berlalu</div>
            @if($infoKontrak['hampir_habis'])
            <div style="margin-top:8px;background:rgba(245,200,0,0.3);border-radius:6px;padding:4px 8px;font-size:11px;font-weight:600">⚠️ Segera berakhir</div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Tagihan Segera --}}
{{-- Perbaikan: Ganti $tagihanBelumBayar jadi $tagihanBelumBayar --}}
@if(isset($tagihanBelumBayar) && $tagihanBelumBayar)
<div style="background:{{ $tagihanBelumBayar->jatuh_tempo->isPast() ? '#fee2e2' : '#fef9c3' }};border:1px solid {{ $tagihanBelumBayar->jatuh_tempo->isPast() ? '#fecaca' : '#fde68a' }};border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div style="display:flex;align-items:center;gap:12px">
        <i class="ri-bill-line" style="font-size:24px;color:{{ $tagihanBelumBayar->jatuh_tempo->isPast() ? '#e53e3e' : '#b7791f' }}"></i>
        <div>
            <div style="font-weight:700;color:{{ $tagihanBelumBayar->jatuh_tempo->isPast() ? '#991b1b' : '#854d0e' }}">
                {{ $tagihanBelumBayar->jatuh_tempo->isPast() ? '⚠️ Tagihan Terlambat!' : '📋 Tagihan Menunggu Pembayaran' }}
            </div>
            <div style="font-size:13px;color:{{ $tagihanBelumBayar->jatuh_tempo->isPast() ? '#7f1d1d' : '#78350f' }}">
                Periode {{ $tagihanBelumBayar->periode }} ·
                Rp {{ number_format($tagihanBelumBayar->jumlah,0,',','.') }} ·
                Jatuh tempo: {{ $tagihanBelumBayar->jatuh_tempo->format('d M Y') }}
            </div>
        </div>
    </div>
    {{-- Route Fix: Ganti ke tagihan.show --}}
    <a href="{{ route('penghuni.tagihan.show', $tagihanBelumBayar->id) }}" class="btn btn-primary btn-sm">Bayar Sekarang</a>
</div>
@endif

@else
<div class="alert alert-info" style="margin-bottom:24px">
    <i class="ri-information-line"></i> Anda belum memiliki kontrak sewa aktif.
</div>
@endif

{{-- Stats Pribadi --}}
<div class="grid grid-3" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ri-money-dollar-circle-line"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format(($statsPersonal['total_bayar'] ?? 0)/1000000,1) }}jt</div>
            <div class="stat-label">Total Pembayaran</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-tools-line"></i></div>
        <div>
            <div class="stat-value">{{ $statsPersonal['total_laporan'] ?? 0 }}</div>
            <div class="stat-label">Total Laporan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cream"><i class="ri-calendar-line"></i></div>
        <div>
            <div class="stat-value">{{ $statsPersonal['lama_menghuni'] ?? 0 }} bln</div>
            <div class="stat-label">Lama Menghuni</div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    {{-- Tagihan Terbaru --}}
    <div class="card">
        <div class="card-title" style="justify-content:space-between">
            <span><i class="ri-bill-line" style="color:var(--green-500)"></i> Tagihan Saya</span>
            {{-- Route Fix: Ganti ke tagihan.index --}}
            <a href="{{ route('penghuni.tagihan.index') }}" style="font-size:12px;color:var(--green-600);text-decoration:none">Semua →</a>
        </div>
        {{-- Perbaikan: Loop $tagihanTerbaru --}}
        @forelse($tagihanTerbaru as $t)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f0f5f1">
            <div>
                <div style="font-size:13px;font-weight:600">{{ $t->periode }}</div>
                <div style="font-size:11px;color:#5a7a5a">Jatuh tempo {{ $t->jatuh_tempo->format('d M Y') }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:13px;font-weight:700;color:var(--green-700)">Rp {{ number_format($t->jumlah,0,',','.') }}</div>
                @if($t->status==='lunas')<span class="badge badge-success" style="font-size:10px">Lunas</span>
                @elseif($t->status==='menunggu_verifikasi')<span class="badge badge-warning" style="font-size:10px">Menunggu</span>
                @else<span class="badge badge-danger" style="font-size:10px">Belum Bayar</span>@endif
            </div>
        </div>
        @empty
        <p style="text-align:center;color:#5a7a5a;font-size:13px;padding:16px">Belum ada tagihan</p>
        @endforelse
    </div>

    {{-- Laporan Kerusakan --}}
    <div class="card">
        <div class="card-title" style="justify-content:space-between">
            <span><i class="ri-tools-line" style="color:var(--green-500)"></i> Laporan Kerusakan</span>
            <a href="{{ route('penghuni.laporan.create') }}" style="font-size:12px;color:var(--green-600);text-decoration:none">+ Lapor →</a>
        </div>
        @forelse($laporanAktif as $l)
        <div style="padding:10px 0;border-bottom:1px solid #f0f5f1">
            <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                <span style="font-size:13px;font-weight:600">{{ Str::limit($l->judul, 35) }}</span>
                @if($l->status==='in_progress')<span class="badge badge-info" style="font-size:10px">Ditangani</span>
                @else<span class="badge badge-danger" style="font-size:10px">Open</span>@endif
            </div>
            <div style="font-size:11px;color:#5a7a5a">{{ ucfirst($l->kategori) }} · {{ $l->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div style="text-align:center;padding:20px;color:#5a7a5a">
            <p style="font-size:13px;margin-bottom:12px">Tidak ada laporan aktif</p>
            @if($kontrakAktif)
            <a href="{{ route('penghuni.laporan.create') }}" class="btn btn-secondary btn-sm">Buat Laporan</a>
            @endif
        </div>
        @endforelse
    </div>
</div>
@endsection