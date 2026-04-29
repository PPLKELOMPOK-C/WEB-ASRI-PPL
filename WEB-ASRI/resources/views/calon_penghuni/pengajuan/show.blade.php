@extends('layouts.app')
@section('title','Detail Pengajuan')
@section('page-title','Detail Pengajuan Sewa')

@section('content')
<div style="max-width:820px">

    {{-- Header Status --}}
    @php $bc=['pending'=>'warning','verifikasi_dokumen'=>'info','jadwal_survei'=>'info','diterima'=>'success','ditolak'=>'danger','dibatalkan'=>'secondary']; @endphp
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <div>
            <div style="font-size:14px;color:#5a7a5a">Pengajuan #{{ $pengajuan->id }}</div>
            <div style="font-size:20px;font-weight:700;color:var(--green-900)">{{ $pengajuan->unit->nama_unit }}</div>
        </div>
        <span class="badge badge-{{ $bc[$pengajuan->status]??'secondary' }}" style="font-size:14px;padding:8px 18px">{{ $pengajuan->status_label }}</span>
    </div>

    {{-- Catatan Admin --}}
    @if($pengajuan->catatan_admin)
    <div class="alert alert-{{ $pengajuan->status==='ditolak'?'error':'info' }}" style="margin-bottom:20px">
        <i class="ri-information-line"></i>
        <strong>Catatan Admin:</strong> {{ $pengajuan->catatan_admin }}
    </div>
    @endif

    {{-- Info Pengajuan --}}
    <div class="card" style="margin-bottom:16px">
        <div class="card-title"><i class="ri-building-line" style="color:var(--green-500)"></i> Informasi Unit</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            @foreach([['Unit',$pengajuan->unit->nama_unit],['Gedung',$pengajuan->unit->gedung],['Wilayah',$pengajuan->unit->wilayah],['Harga/Bulan','Rp '.number_format($pengajuan->unit->harga_sewa,0,',','.')],['Durasi Sewa',$pengajuan->durasi_sewa.' bulan'],['Tgl Mulai',$pengajuan->tanggal_mulai?$pengajuan->tanggal_mulai->format('d M Y'):'-'],['Tgl Pengajuan',$pengajuan->created_at->format('d M Y H:i')],['Status',$pengajuan->status_label]] as [$k,$v])
            <div style="background:var(--green-50);padding:12px;border-radius:8px">
                <div style="font-size:11px;color:#5a7a5a;font-weight:600;text-transform:uppercase">{{ $k }}</div>
                <div style="font-size:14px;font-weight:600;color:var(--green-900);margin-top:2px">{{ $v }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Dokumen --}}
    <div class="card" style="margin-bottom:16px">
        <div class="card-title" style="justify-content:space-between">
            <span><i class="ri-folder-open-line" style="color:var(--green-500)"></i> Dokumen Persyaratan</span>
            @if(in_array($pengajuan->status, ['pending','verifikasi_dokumen']))
            <a href="{{ route('calon.dokumen.index', $pengajuan->id) }}" class="btn btn-secondary btn-sm"><i class="ri-upload-line"></i> Kelola Dokumen</a>
            @endif
        </div>
        @if($pengajuan->dokumens->isEmpty())
        <div style="text-align:center;padding:20px;color:#5a7a5a">
            <p style="font-size:13px;margin-bottom:12px">Belum ada dokumen diunggah</p>
            <a href="{{ route('calon.dokumen.index', $pengajuan->id) }}" class="btn btn-primary btn-sm"><i class="ri-upload-line"></i> Upload Sekarang</a>
        </div>
        @else
        <div style="display:grid;gap:8px">
        @foreach($pengajuan->dokumens as $d)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:var(--green-50);border-radius:8px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="{{ str_contains($d->mime_type,'pdf') ? 'ri-file-pdf-line' : 'ri-image-line' }}" style="font-size:20px;color:var(--green-600)"></i>
                <div>
                    <div style="font-size:13px;font-weight:600">{{ $d->jenis_label }}</div>
                    <div style="font-size:11px;color:#5a7a5a">{{ $d->nama_file }} · {{ $d->ukuran_file }} KB</div>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <span class="badge badge-{{ $d->status==='verified'?'success':($d->status==='rejected'?'danger':'warning') }}">{{ ucfirst($d->status) }}</span>
                <a href="{{ Storage::url($d->path_file) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="ri-external-link-line"></i></a>
            </div>
        </div>
        @endforeach
        </div>
        @endif
    </div>

    {{-- Jadwal Survei --}}
    @if($pengajuan->status === 'jadwal_survei')
    <div class="card" style="margin-bottom:16px">
        <div class="card-title"><i class="ri-calendar-event-line" style="color:var(--green-500)"></i> Jadwal Survei</div>
        @if($pengajuan->jadwalSurvei)
            <div style="padding:16px;background:var(--green-50);border-radius:8px">
                <div style="font-size:16px;font-weight:700;color:var(--green-800)">
                    {{ \Carbon\Carbon::parse($pengajuan->jadwalSurvei->tanggal_survei)->format('l, d M Y - H:i') }}
                </div>
                <div style="margin-top:6px">
                    Status: <span class="badge badge-{{ $pengajuan->jadwalSurvei->status==='dikonfirmasi'?'success':'warning' }}">{{ ucfirst($pengajuan->jadwalSurvei->status) }}</span>
                </div>
                @if($pengajuan->jadwalSurvei->status==='pending')
                <div style="margin-top:10px;font-size:13px;color:#5a7a5a">Menunggu konfirmasi dari admin.</div>
                @endif
            </div>
        @else
            <div style="text-align:center;padding:20px">
                <p style="font-size:13px;color:#5a7a5a;margin-bottom:12px">Silakan pilih jadwal survei Anda</p>
                <a href="{{ route('calon.jadwal.index', $pengajuan->id) }}" class="btn btn-primary"><i class="ri-calendar-check-line"></i> Pilih Jadwal Survei</a>
            </div>
        @endif
    </div>
    @endif

    <div style="display:flex;gap:12px">
        <a href="{{ route('calon.pengajuan.index') }}" class="btn btn-secondary">← Kembali</a>
        @if(in_array($pengajuan->status,['pending','draft']))
        <form method="POST" action="{{ route('calon.pengajuan.batalkan', $pengajuan->id) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
            @csrf
            <button type="submit" class="btn btn-danger">Batalkan Pengajuan</button>
        </form>
        @endif
    </div>
</div>
@endsection
