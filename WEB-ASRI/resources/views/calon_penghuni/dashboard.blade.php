@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard Saya')

@section('content')

{{-- Banner Status Pengajuan Aktif --}}
@if($pengajuanAktif)
<div style="background:linear-gradient(135deg,var(--green-800),var(--green-600));border-radius:14px;padding:24px;margin-bottom:24px;color:white">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div>
            <div style="font-size:12px;font-weight:600;opacity:0.75;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">Pengajuan Aktif</div>
            <div style="font-size:20px;font-weight:700">{{ $pengajuanAktif->unit->nama_unit }}</div>
            <div style="font-size:13px;opacity:0.8;margin-top:4px">{{ $pengajuanAktif->unit->gedung }} · {{ $pengajuanAktif->unit->wilayah }}</div>
        </div>
        <div style="text-align:right">
            <span style="background:rgba(255,255,255,0.2);padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600">
                {{ $pengajuanAktif->status_label }}
            </span>
            <div style="margin-top:10px">
                <a href="{{ route('calon.pengajuan.show', $pengajuanAktif->id) }}" style="color:white;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,0.5);padding:6px 14px;border-radius:8px">
                    Lihat Detail →
                </a>
            </div>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div style="margin-top:20px;display:flex;gap:4px">
        @foreach($langkahProses as $i => $langkah)
        <div style="flex:1;text-align:center">
            <div style="display:flex;align-items:center">
                @if($i > 0)<div style="flex:1;height:2px;background:{{ $langkah['status']==='done' ? 'rgba(255,255,255,0.8)' : 'rgba(255,255,255,0.25)' }}"></div>@endif
                <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;
                    background:{{ $langkah['status']==='done' ? 'white' : ($langkah['status']==='active' ? 'rgba(255,255,255,0.4)' : 'rgba(255,255,255,0.15)') }};
                    color:{{ $langkah['status']==='done' ? 'var(--green-700)' : 'white' }};
                    border:{{ $langkah['status']==='active' ? '2px solid white' : 'none' }}">
                    {{ $langkah['status']==='done' ? '✓' : ($i+1) }}
                </div>
                @if($i < count($langkahProses)-1)<div style="flex:1;height:2px;background:{{ $langkah['status']==='done' ? 'rgba(255,255,255,0.8)' : 'rgba(255,255,255,0.25)' }}"></div>@endif
            </div>
            <div style="font-size:10px;margin-top:5px;opacity:{{ $langkah['status']==='active' ? '1' : '0.65' }};font-weight:{{ $langkah['status']==='active' ? '700' : '400' }}">
                {{ $langkah['label'] }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- Aksi cepat berdasarkan status --}}
    @if($pengajuanAktif->status === 'pending' && $pengajuanAktif->dokumens->count() < 3)
    <div style="margin-top:16px;padding:12px;background:rgba(255,255,255,0.15);border-radius:8px">
        <div style="font-size:13px;font-weight:600;margin-bottom:8px">⚠️ Dokumen belum lengkap</div>
        <a href="{{ route('calon.dokumen.index', $pengajuanAktif->id) }}" style="background:white;color:var(--green-700);padding:7px 16px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">
            Upload Dokumen Sekarang
        </a>
    </div>
    @elseif($pengajuanAktif->status === 'jadwal_survei' && !$pengajuanAktif->jadwalSurvei)
    <div style="margin-top:16px;padding:12px;background:rgba(255,255,255,0.15);border-radius:8px">
        <div style="font-size:13px;font-weight:600;margin-bottom:8px">📅 Pilih jadwal survei Anda</div>
        <a href="{{ route('calon.jadwal.index', $pengajuanAktif->id) }}" style="background:white;color:var(--green-700);padding:7px 16px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">
            Pilih Jadwal
        </a>
    </div>
    @endif
</div>

@else
{{-- Belum ada pengajuan --}}
<div style="background:var(--cream-100);border:2px dashed var(--green-300);border-radius:14px;padding:32px;text-align:center;margin-bottom:24px">
    <i class="ri-home-smile-line" style="font-size:48px;color:var(--green-400);margin-bottom:12px;display:block"></i>
    <div style="font-size:17px;font-weight:700;color:var(--green-900);margin-bottom:6px">Belum Ada Pengajuan Sewa</div>
    <div style="font-size:14px;color:#5a7a5a;margin-bottom:20px">Mulai cari unit rusun impian Anda di Jakarta</div>
    <a href="{{ route('public.units') }}" class="btn btn-primary"><i class="ri-search-line"></i> Cari Unit Tersedia</a>
</div>
@endif

<div class="grid grid-2">
    {{-- Riwayat Pengajuan --}}
    <div class="card">
        <div class="card-title" style="justify-content:space-between">
            <span><i class="ri-history-line" style="color:var(--green-500)"></i> Riwayat Pengajuan</span>
            <a href="{{ route('calon.pengajuan.index') }}" style="font-size:12px;color:var(--green-600);text-decoration:none">Semua →</a>
        </div>
        @forelse($riwayatPengajuan as $p)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f0f5f1">
            <div>
                <div style="font-size:13px;font-weight:600">{{ $p->unit->nama_unit }}</div>
                <div style="font-size:11px;color:#5a7a5a">{{ $p->created_at->format('d M Y') }} · {{ $p->durasi_sewa }} bulan</div>
            </div>
            @php $bc=['pending'=>'warning','verifikasi_dokumen'=>'info','jadwal_survei'=>'info','diterima'=>'success','ditolak'=>'danger','dibatalkan'=>'secondary']; @endphp
            <span class="badge badge-{{ $bc[$p->status]??'secondary' }}" style="font-size:10px">{{ $p->status_label }}</span>
        </div>
        @empty
        <p style="text-align:center;color:#5a7a5a;font-size:13px;padding:16px">Belum ada riwayat</p>
        @endforelse
    </div>

    {{-- Notifikasi Terbaru --}}
    <div class="card">
        <div class="card-title" style="justify-content:space-between">
            <span><i class="ri-notification-3-line" style="color:var(--green-500)"></i> Notifikasi Terbaru</span>
            <a href="{{ route('notifikasi.index') }}" style="font-size:12px;color:var(--green-600);text-decoration:none">Semua →</a>
        </div>
        @forelse($notifikasiTerbaru as $n)
        <div style="padding:10px 0;border-bottom:1px solid #f0f5f1">
            <div style="display:flex;gap:8px">
                <div style="width:8px;height:8px;background:{{ ['info'=>'#3b82f6','success'=>'#22c55e','warning'=>'#f59e0b','danger'=>'#ef4444'][$n->tipe]??'#6b7280' }};border-radius:50%;margin-top:5px;flex-shrink:0"></div>
                <div>
                    <div style="font-size:13px;font-weight:600">{{ $n->judul }}</div>
                    <div style="font-size:12px;color:#5a7a5a">{{ $n->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
        @empty
        <p style="text-align:center;color:#5a7a5a;font-size:13px;padding:16px">Tidak ada notifikasi baru</p>
        @endforelse
    </div>
</div>

{{-- Unit Rekomendasi --}}
<div class="card" style="margin-top:20px">
    <div class="card-title"><i class="ri-star-line" style="color:var(--green-500)"></i> Unit yang Mungkin Anda Sukai</div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        @foreach($unitRekomendasi as $u)
        <div style="border:1px solid #e8f0eb;border-radius:10px;overflow:hidden">
            <div style="height:120px;background:var(--green-100);display:flex;align-items:center;justify-content:center">
                @if($u->gambar)
                <img src="{{ Storage::url($u->gambar) }}" style="width:100%;height:100%;object-fit:cover">
                @else
                <i class="ri-building-4-line" style="font-size:36px;color:var(--green-400)"></i>
                @endif
            </div>
            <div style="padding:12px">
                <div style="font-size:13px;font-weight:700;color:var(--green-900)">{{ $u->nama_unit }}</div>
                <div style="font-size:12px;color:#5a7a5a;margin-top:2px">{{ $u->wilayah }}</div>
                <div style="font-size:14px;font-weight:700;color:var(--green-700);margin-top:6px">Rp {{ number_format($u->harga_sewa,0,',','.') }}/bln</div>
                <a href="{{ route('public.unit.detail', $u) }}" class="btn btn-secondary btn-sm" style="margin-top:8px;width:100%;justify-content:center">Lihat Detail</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
