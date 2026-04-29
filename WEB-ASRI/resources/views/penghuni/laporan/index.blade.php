@extends('layouts.app')
@section('title','Laporan Kerusakan')
@section('page-title','Laporan Kerusakan Saya')

@section('content')

<div class="grid grid-3" style="margin-bottom:20px">
    @foreach([['Open','open','danger'],['Ditangani','in_progress','info'],['Selesai','resolved','success']] as [$l,$k,$c])
    <div class="stat-card">
        <div class="stat-icon {{ $c==='success'?'green':($c==='info'?'teal':'orange') }}"><i class="ri-tools-line"></i></div>
        <div><div class="stat-value">{{ $summary[$k] }}</div><div class="stat-label">{{ $l }}</div></div>
    </div>
    @endforeach
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:12px">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
            <select name="status" class="form-control" style="width:160px">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            </select>
            <select name="kategori" class="form-control" style="width:160px">
                <option value="">Semua Kategori</option>
                @foreach(['listrik','plumbing','struktur','fasilitas','lainnya'] as $k)
                <option value="{{ $k }}" {{ request('kategori')==$k?'selected':'' }}>{{ ucfirst($k) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
        <a href="{{ route('penghuni.laporan.create') }}" class="btn btn-primary"><i class="ri-add-line"></i> Lapor Kerusakan</a>
    </div>

    <div style="display:grid;gap:12px">
    @forelse($laporan as $l)
    <div style="border:1px solid #e8f0eb;border-radius:10px;padding:16px">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--green-900)">{{ $l->judul }}</div>
                <div style="margin-top:5px;display:flex;gap:8px;flex-wrap:wrap">
                    <span style="font-size:12px;background:var(--green-50);color:var(--green-700);padding:2px 10px;border-radius:10px;font-weight:600">{{ ucfirst($l->kategori) }}</span>
                    <span style="font-size:12px;color:#5a7a5a">{{ $l->created_at->format('d M Y') }}</span>
                </div>
                @if($l->respon_admin)
                <div style="margin-top:8px;font-size:12px;color:#5a7a5a;background:var(--green-50);padding:8px 12px;border-radius:6px;border-left:3px solid var(--green-400)">
                    <strong>Respon Admin:</strong> {{ Str::limit($l->respon_admin, 80) }}
                </div>
                @endif
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
                @if($l->status==='open')<span class="badge badge-danger">Open</span>
                @elseif($l->status==='in_progress')<span class="badge badge-info">Ditangani</span>
                @elseif($l->status==='resolved')<span class="badge badge-success">Selesai</span>
                @else<span class="badge badge-secondary">Closed</span>@endif
                <div style="display:flex;gap:6px">
                    <a href="{{ route('penghuni.laporan.show', $l->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                    @if(in_array($l->status,['open','resolved']))
                    <form method="POST" action="{{ route('penghuni.laporan.close', $l->id) }}" onsubmit="return confirm('Tutup laporan ini?')">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Tutup Tiket</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:48px;color:#5a7a5a">
        <i class="ri-tools-line" style="font-size:40px;display:block;opacity:0.3;margin-bottom:10px"></i>
        <div style="font-size:15px;font-weight:600;margin-bottom:8px">Tidak ada laporan</div>
        <a href="{{ route('penghuni.laporan.create') }}" class="btn btn-primary">Buat Laporan Pertama</a>
    </div>
    @endforelse
    </div>
    <div style="margin-top:16px">{{ $laporan->links() }}</div>
</div>
@endsection
```

