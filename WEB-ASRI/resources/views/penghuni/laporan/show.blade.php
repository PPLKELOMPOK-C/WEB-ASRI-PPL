@extends('layouts.app')
@section('title','Detail Laporan')
@section('page-title','Detail Laporan Kerusakan')

@section('content')
<div style="max-width:680px">
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <div>
            <div style="font-size:20px;font-weight:700;color:var(--green-900)">{{ $laporan->judul }}</div>
            <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap">
                <span style="font-size:12px;background:var(--green-50);color:var(--green-700);padding:3px 10px;border-radius:12px;font-weight:600">{{ ucfirst($laporan->kategori) }}</span>
                <span style="font-size:12px;color:#5a7a5a">{{ $laporan->unit->nama_unit }}</span>
                <span style="font-size:12px;color:#5a7a5a">{{ $laporan->created_at->format('d M Y H:i') }}</span>
            </div>
        </div>
        @if($laporan->status==='open')<span class="badge badge-danger" style="font-size:13px;padding:6px 14px">Open</span>
        @elseif($laporan->status==='in_progress')<span class="badge badge-info" style="font-size:13px;padding:6px 14px">Sedang Ditangani</span>
        @elseif($laporan->status==='resolved')<span class="badge badge-success" style="font-size:13px;padding:6px 14px">Selesai ✓</span>
        @else<span class="badge badge-secondary" style="font-size:13px;padding:6px 14px">Closed</span>@endif
    </div>

    <div style="font-size:14px;color:#2d3d2d;line-height:1.8;margin-bottom:18px">{{ $laporan->deskripsi }}</div>

    @if($laporan->foto)
    <div style="margin-bottom:20px">
        <div style="font-size:13px;font-weight:600;color:var(--green-800);margin-bottom:8px">Foto Kerusakan:</div>
        <img src="{{ Storage::url($laporan->foto) }}" style="max-width:100%;max-height:360px;border-radius:10px;border:1px solid #e8f0eb">
    </div>
    @endif

    @if($laporan->respon_admin)
    <div style="padding:16px;background:var(--green-50);border-radius:10px;border-left:4px solid var(--green-500);margin-bottom:18px">
        <div style="font-size:12px;font-weight:700;color:var(--green-700);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px">Respon Teknisi/Admin</div>
        <div style="font-size:14px;color:#2d3d2d;line-height:1.7">{{ $laporan->respon_admin }}</div>
        @if($laporan->resolved_at)
        <div style="font-size:12px;color:#5a7a5a;margin-top:8px">Diselesaikan: {{ $laporan->resolved_at->format('d M Y H:i') }}</div>
        @endif
    </div>
    @endif

    <div style="display:flex;gap:12px">
        <a href="{{ route('penghuni.laporan.index') }}" class="btn btn-secondary">← Kembali</a>
        @if(in_array($laporan->status,['open','resolved']))
        <form method="POST" action="{{ route('penghuni.laporan.close', $laporan->id) }}" onsubmit="return confirm('Tutup tiket laporan ini?')">
            @csrf
            <button type="submit" class="btn btn-secondary">Tutup Tiket</button>
        </form>
        @endif
    </div>
</div>
</div>
@endsection
