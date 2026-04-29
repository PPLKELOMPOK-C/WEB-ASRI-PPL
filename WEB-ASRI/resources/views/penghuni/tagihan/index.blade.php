@extends('layouts.app')
@section('title','Tagihan Saya')
@section('page-title','Tagihan Sewa')

@section('content')

<div class="grid grid-3" style="margin-bottom:20px">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-money-dollar-circle-line"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($summary['total_belum_bayar']/1000000,1) }}jt</div>
            <div class="stat-label">Belum Dibayar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ri-checkbox-circle-line"></i></div>
        <div>
            <div class="stat-value">{{ $summary['total_lunas'] }}</div>
            <div class="stat-label">Sudah Lunas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ri-time-line"></i></div>
        <div>
            <div class="stat-value">{{ $summary['tagihan_terlambat'] }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>
</div>

@if($tagihanSegera && !$tagihanSegera->jatuh_tempo->isPast())
<div class="alert alert-info" style="margin-bottom:20px">
    <i class="ri-calendar-event-line"></i>
    Tagihan bulan <strong>{{ $tagihanSegera->periode }}</strong> jatuh tempo pada <strong>{{ $tagihanSegera->jatuh_tempo->format('d M Y') }}</strong>
    ({{ $tagihanSegera->jatuh_tempo->diffInDays(now()) }} hari lagi).
    <a href="{{ route('penghuni.tagihan.show', $tagihanSegera->id) }}" style="color:inherit;font-weight:700;margin-left:8px">Bayar →</a>
</div>
@endif

<div class="card">
    <form method="GET" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
        <select name="status" class="form-control" style="width:200px">
            <option value="">Semua Status</option>
            <option value="belum_bayar" {{ request('status')=='belum_bayar'?'selected':'' }}>Belum Bayar</option>
            <option value="menunggu_verifikasi" {{ request('status')=='menunggu_verifikasi'?'selected':'' }}>Menunggu Verifikasi</option>
            <option value="lunas" {{ request('status')=='lunas'?'selected':'' }}>Lunas</option>
        </select>
        <input type="number" name="tahun" placeholder="Tahun (mis. 2024)" class="form-control" style="width:180px" value="{{ request('tahun') }}" min="2020" max="{{ date('Y') }}">
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    <div style="display:grid;gap:12px">
    @forelse($tagihans as $t)
    <div style="border:1px solid {{ $t->status==='lunas'?'var(--green-200)':($t->jatuh_tempo->isPast()&&$t->status==='belum_bayar'?'#fecaca':'#e8f0eb') }};border-radius:10px;padding:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div style="font-size:16px;font-weight:700;color:var(--green-900)">Tagihan {{ $t->periode }}</div>
            <div style="font-size:12px;color:#5a7a5a;margin-top:2px">
                Jatuh tempo: {{ $t->jatuh_tempo->format('d M Y') }}
                @if($t->jatuh_tempo->isPast() && $t->status==='belum_bayar')
                <span style="color:#e53e3e;font-weight:600"> (Terlambat {{ $t->jatuh_tempo->diffInDays(now()) }} hari)</span>
                @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px">
            <div style="text-align:right">
                <div style="font-size:18px;font-weight:700;color:var(--green-800)">Rp {{ number_format($t->jumlah,0,',','.') }}</div>
                @if($t->status==='lunas')<span class="badge badge-success">Lunas</span>
                @elseif($t->status==='menunggu_verifikasi')<span class="badge badge-warning">Menunggu Verif.</span>
                @else<span class="badge badge-danger">Belum Bayar</span>@endif
            </div>
            <a href="{{ route('penghuni.tagihan.show', $t->id) }}" class="btn {{ $t->status==='belum_bayar' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                {{ $t->status==='belum_bayar' ? 'Bayar' : 'Detail' }}
            </a>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:48px;color:#5a7a5a">
        <i class="ri-bill-line" style="font-size:40px;display:block;opacity:0.3;margin-bottom:10px"></i>
        <div style="font-size:15px;font-weight:600">Belum ada tagihan</div>
    </div>
    @endforelse
    </div>
    <div style="margin-top:16px">{{ $tagihans->links() }}</div>
</div>
@endsection
