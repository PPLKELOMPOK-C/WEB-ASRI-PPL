@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Saya')

@section('content')

{{-- Banner Status Pengajuan Aktif --}}
@if($pengajuanAktif)
<div style="background: linear-gradient(135deg, #1e4d2b, #2d5a27); border-radius: 16px; padding: 28px; margin-bottom: 24px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 11px; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 8px;">
                Pengajuan Aktif
            </div>
            <div style="font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">{{ $pengajuanAktif->unit->nama_unit }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                <i class="ri-map-pin-2-line"></i> {{ $pengajuanAktif->unit->gedung }} · {{ $pengajuanAktif->unit->wilayah }}
            </div>
        </div>
        <div style="text-align: right;">
            <div style="background: rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 30px; font-size: 13px; font-weight: 700; backdrop-filter: blur(4px); display: inline-block;">
                {{ $pengajuanAktif->status_label }}
            </div>
            <div style="margin-top: 14px;">
                <a href="{{ route('calon.pengajuan.show', $pengajuanAktif->id) }}" style="color: white; font-size: 13px; font-weight: 600; text-decoration: none; border: 1.5px solid rgba(255,255,255,0.4); padding: 8px 18px; border-radius: 10px; transition: 0.3s; display: inline-block;">
                    Lihat Detail Pengajuan <i class="ri-arrow-right-line" style="vertical-align: middle; margin-left: 4px;"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Progress Steps Section --}}
    <div style="margin-top: 32px; display: flex; position: relative; justify-content: space-between;">
        @foreach($langkahProses as $i => $langkah)
            <div style="flex: 1; position: relative; text-align: center;">
                {{-- Connector Line --}}
                @if($i < count($langkahProses) - 1)
                    <div style="position: absolute; top: 14px; left: 50%; width: 100%; height: 3px; 
                        background: {{ $langkah['status'] === 'done' ? '#ffffff' : 'rgba(255,255,255,0.2)' }}; 
                        z-index: 1;">
                    </div>
                @endif

                {{-- Step Circle --}}
                <div style="position: relative; z-index: 2; width: 32px; height: 32px; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800;
                    background: {{ $langkah['status'] === 'done' ? '#ffffff' : ($langkah['status'] === 'active' ? 'rgba(255,255,255,0.3)' : '#1e4d2b') }};
                    color: {{ $langkah['status'] === 'done' ? '#1e4d2b' : '#ffffff' }};
                    border: 2px solid {{ $langkah['status'] === 'active' ? '#ffffff' : ($langkah['status'] === 'done' ? '#ffffff' : 'rgba(255,255,255,0.4)') }};
                    box-shadow: {{ $langkah['status'] === 'active' ? '0 0 10px rgba(255,255,255,0.5)' : 'none' }};">
                    {!! $langkah['status'] === 'done' ? '<i class="ri-check-line"></i>' : ($i + 1) !!}
                </div>

                {{-- Step Label --}}
                <div style="margin-top: 10px; font-size: 11px; font-weight: {{ $langkah['status'] === 'active' ? '700' : '500' }}; opacity: {{ $langkah['status'] === 'waiting' ? '0.5' : '1' }};">
                    {{ $langkah['label'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Actionable Alert --}}
    @if($pengajuanAktif->status === 'pending' && $pengajuanAktif->dokumens->count() < 3)
        <div style="margin-top: 24px; padding: 14px 20px; background: rgba(255,255,255,0.1); border-left: 4px solid #facc15; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 13px;"><i class="ri-error-warning-fill" style="color: #facc15; margin-right: 8px;"></i> Dokumen persyaratan Anda belum lengkap.</span>
            <a href="{{ route('calon.dokumen.index', $pengajuanAktif->id) }}" style="background: white; color: #1e4d2b; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">Upload Sekarang</a>
        </div>
    @endif
</div>
@else
{{-- Empty State --}}
<div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 48px; text-align: center; margin-bottom: 24px;">
    <div style="width: 64px; height: 64px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <i class="ri-home-4-line" style="font-size: 32px; color: #64748b;"></i>
    </div>
    <div style="font-size: 18px; font-weight: 700; color: #1e293b;">Belum Ada Pengajuan Sewa</div>
    <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">Mulai cari unit rusun impian Anda di aplikasi ASRI.</p>
    <a href="{{ route('public.units') }}" class="btn btn-primary" style="padding: 10px 24px; border-radius: 10px;">
        <i class="ri-search-line"></i> Cari Unit Tersedia
    </a>
</div>
@endif

<div class="grid grid-2" style="gap: 24px;">
    {{-- Riwayat Pengajuan --}}
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div class="card-title" style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; color: #334155;"><i class="ri-history-line" style="color: #1e4d2b; margin-right: 8px;"></i> Riwayat Pengajuan</span>
            <a href="{{ route('calon.pengajuan.index') }}" style="font-size: 12px; color: #1e4d2b; font-weight: 600; text-decoration: none;">Lihat Semua</a>
        </div>
        <div style="padding: 10px 20px;">
            @forelse($riwayatPengajuan as $p)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f8fafc;">
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $p->unit->nama_unit }}</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $p->created_at->format('d M Y') }} · {{ $p->durasi_sewa }} Bulan</div>
                    </div>
                    @php $bc=['pending'=>'warning','verifikasi_dokumen'=>'info','jadwal_survei'=>'info','diterima'=>'success','ditolak'=>'danger','dibatalkan'=>'secondary']; @endphp
                    <span class="badge badge-{{ $bc[$p->status] ?? 'secondary' }}" style="font-size: 10px; padding: 5px 10px; border-radius: 6px;">{{ $p->status_label }}</span>
                </div>
            @empty
                <div style="text-align: center; padding: 32px 0;">
                    <p style="color: #94a3b8; font-size: 13px;">Belum ada riwayat pengajuan.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Notifikasi --}}
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div class="card-title" style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; color: #334155;"><i class="ri-notification-3-line" style="color: #1e4d2b; margin-right: 8px;"></i> Notifikasi Terbaru</span>
            <a href="{{ route('notifikasi.index') }}" style="font-size: 12px; color: #1e4d2b; font-weight: 600; text-decoration: none;">Semua</a>
        </div>
        <div style="padding: 10px 20px;">
            @forelse($notifikasiTerbaru as $n)
                <div style="padding: 14px 0; border-bottom: 1px solid #f8fafc; display: flex; gap: 12px;">
                    <div style="width: 10px; height: 10px; background: {{ ['info'=>'#3b82f6','success'=>'#22c55e','warning'=>'#f59e0b','danger'=>'#ef4444'][$n->tipe] ?? '#64748b' }}; border-radius: 50%; margin-top: 5px; flex-shrink: 0;"></div>
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1.4;">{{ $n->judul }}</div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 32px 0;">
                    <p style="color: #94a3b8; font-size: 13px;">Tidak ada notifikasi baru.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection