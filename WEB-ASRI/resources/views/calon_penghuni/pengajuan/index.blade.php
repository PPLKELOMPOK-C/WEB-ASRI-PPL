@extends('layouts.app')
@section('title','Pengajuan Saya')
@section('page-title','Riwayat Pengajuan Sewa')

@section('content')

<div style="display:flex;justify-content:flex-end;margin-bottom:16px">
    <a href="{{ route('public.units') }}" class="btn btn-primary"><i class="ri-search-line"></i> Cari Unit Baru</a>
</div>

<div class="card">
    @forelse($pengajuans as $p)
    <div style="border:1px solid #e8f0eb;border-radius:12px;padding:18px;margin-bottom:14px;transition:box-shadow 0.2s" onmouseover="this.style.boxShadow='0 4px 16px rgba(30,124,70,0.1)'" onmouseout="this.style.boxShadow='none'">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div>
                <div style="font-size:16px;font-weight:700;color:var(--green-900)">{{ $p->unit->nama_unit }}</div>
                <div style="font-size:13px;color:#5a7a5a;margin-top:2px">{{ $p->unit->gedung }} · {{ $p->unit->wilayah }}</div>
                <div style="margin-top:8px;font-size:13px;color:#4a5a4a">
                    Durasi: <strong>{{ $p->durasi_sewa }} bulan</strong>
                    @if($p->tanggal_mulai) · Mulai: <strong>{{ $p->tanggal_mulai->format('d M Y') }}</strong> @endif
                    · Diajukan: <strong>{{ $p->created_at->format('d M Y') }}</strong>
                </div>
                @if($p->catatan_admin && in_array($p->status, ['ditolak', 'verifikasi_dokumen']))
                <div style="margin-top:8px;padding:8px 12px;background:#fef9c3;border-radius:6px;font-size:12px;color:#854d0e">
                    <strong>Catatan Admin:</strong> {{ $p->catatan_admin }}
                </div>
                @endif
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
                @php $bc=['pending'=>'warning','verifikasi_dokumen'=>'info','jadwal_survei'=>'info','diterima'=>'success','ditolak'=>'danger','dibatalkan'=>'secondary']; @endphp
                <span class="badge badge-{{ $bc[$p->status]??'secondary' }}" style="font-size:12px;padding:5px 12px">{{ $p->status_label }}</span>
                <div style="display:flex;gap:8px">
                    <a href="{{ route('calon.pengajuan.show', $p->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                    @if(in_array($p->status, ['pending','draft']))
                    <form method="POST" action="{{ route('calon.pengajuan.batalkan', $p->id) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Indikator dokumen --}}
        <div style="margin-top:12px;display:flex;gap:6px;flex-wrap:wrap">
            @foreach(['ktp','kk','slip_gaji','surat_keterangan'] as $jenis)
            @php $uploaded = $p->dokumens->where('jenis_dokumen',$jenis)->first(); @endphp
            <div style="font-size:11px;padding:3px 10px;border-radius:12px;font-weight:600;background:{{ $uploaded ? 'var(--green-100)' : '#f3f4f6' }};color:{{ $uploaded ? 'var(--green-700)' : '#9ca3af' }}">
                {{ $uploaded ? '✓' : '○' }} {{ strtoupper($jenis) }}
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:60px;color:#5a7a5a">
        <i class="ri-file-list-3-line" style="font-size:48px;display:block;opacity:0.3;margin-bottom:12px"></i>
        <div style="font-size:16px;font-weight:600;margin-bottom:8px">Belum Ada Pengajuan</div>
        <p style="font-size:14px;margin-bottom:20px">Anda belum pernah mengajukan sewa unit.</p>
        <a href="{{ route('public.units') }}" class="btn btn-primary">Cari Unit Sekarang</a>
    </div>
    @endforelse
    <div style="margin-top:8px">{{ $pengajuans->links() }}</div>
</div>
@endsection
