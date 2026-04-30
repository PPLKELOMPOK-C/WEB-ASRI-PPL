```blade
@extends('layouts.public')
@section('title','Cari Unit Rusun')

@section('content')
<div style="background:var(--green-900);padding:40px 5%">
    <div class="container" style="padding:0">
        <h1 style="font-family:'Playfair Display',serif;font-size:30px;font-weight:700;color:white;margin-bottom:20px">Cari Unit Rusun</h1>
        <form method="GET" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:end;flex-wrap:wrap">
            <div>
                <label style="font-size:12px;color:rgba(255,255,255,0.75);font-weight:600;display:block;margin-bottom:6px">Kata Kunci</label>
                <input type="text" name="search" placeholder="Gedung, blok, alamat..." value="{{ request('search') }}" style="width:100%;padding:10px 14px;border-radius:8px;border:none;font-family:inherit;font-size:14px">
            </div>
            <div>
                <label style="font-size:12px;color:rgba(255,255,255,0.75);font-weight:600;display:block;margin-bottom:6px">Wilayah</label>
                <select name="wilayah" style="width:100%;padding:10px 14px;border-radius:8px;border:none;font-family:inherit;font-size:14px">
                    <option value="">Semua Wilayah</option>
                    @foreach($wilayahs as $w)
                    <option value="{{ $w }}" {{ request('wilayah')==$w?'selected':'' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
                <label style="font-size:12px;color:rgba(255,255,255,0.75);font-weight:600">Harga Maks. (Rp)</label>
                <input type="number" name="harga_max" placeholder="Contoh: 1500000" value="{{ request('harga_max') }}" style="width:100%;padding:10px 14px;border-radius:8px;border:none;font-family:inherit;font-size:14px">
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
                <label style="font-size:12px;color:rgba(255,255,255,0.75);font-weight:600">.</label>
                <div style="display:flex;gap:8px">
                    <label style="display:flex;align-items:center;gap:6px;color:white;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="tersedia" value="1" {{ request('tersedia')?'checked':'' }} style="width:16px;height:16px;accent-color:var(--green-400)"> Tersedia saja
                    </label>
                </div>
                <button type="submit" style="background:var(--green-400);color:white;border:none;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit">Cari</button>
            </div>
        </form>
        @if(request()->anyFilled(['search','wilayah','harga_max','tersedia']))
        <div style="margin-top:12px">
            <a href="{{ route('public.units') }}" style="color:rgba(255,255,255,0.7);font-size:13px;text-decoration:none">× Reset filter</a>
            <span style="color:rgba(255,255,255,0.6);font-size:13px;margin-left:12px">{{ $units->total() }} unit ditemukan</span>
        </div>
        @endif
    </div>
</div>

<div style="padding:48px 5%">
    <div class="container" style="padding:0">
        @if($units->isEmpty())
        <div style="text-align:center;padding:64px;color:#5a7a5a;background:white;border-radius:14px;border:1px solid #e8f0eb">
            <i class="ri-search-line" style="font-size:48px;display:block;opacity:0.3;margin-bottom:12px"></i>
            <div style="font-size:17px;font-weight:700;margin-bottom:6px">Unit Tidak Ditemukan</div>
            <p>Coba ubah kriteria pencarian atau reset filter</p>
            <a href="{{ route('public.units') }}" style="display:inline-block;margin-top:16px;color:var(--green-600);font-weight:600;text-decoration:none">Reset Filter</a>
        </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:32px">
            @foreach($units as $unit)
            <div style="background:white;border-radius:14px;overflow:hidden;border:1px solid {{ $unit->status==='tersedia'?'var(--green-100)':'#e8f0eb' }};box-shadow:0 2px 8px rgba(30,124,70,0.06);transition:all 0.25s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 24px rgba(30,124,70,0.12)'" onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(30,124,70,0.06)'">
                <div style="height:175px;background:var(--green-100);overflow:hidden;position:relative">
                    @if($unit->gambar)
                    <img src="{{ Storage::url($unit->gambar) }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                    <div style="display:flex;align-items:center;justify-content:center;height:100%"><i class="ri-building-4-line" style="font-size:48px;color:var(--green-400)"></i></div>
                    @endif
                    <div style="position:absolute;top:10px;right:10px">
                        @if($unit->status==='tersedia')<span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px">Tersedia</span>
                        @elseif($unit->status==='dihuni')<span style="background:#dbeafe;color:#1e40af;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px">Dihuni</span>
                        @else<span style="background:#fef9c3;color:#854d0e;font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px">Maintenance</span>@endif
                    </div>
                </div>
                <div style="padding:16px">
                    <div style="font-size:11px;font-weight:700;color:var(--green-600);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px">{{ $unit->wilayah }}</div>
                    <h3 style="font-size:15px;font-weight:700;color:var(--green-900);margin-bottom:3px">{{ $unit->nama_unit }}</h3>
                    <p style="font-size:12px;color:#5a7a5a;margin-bottom:12px">{{ $unit->gedung }}{{ $unit->luas_m2 ? ' · '.$unit->luas_m2.' m²' : '' }}</p>
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <div style="font-size:18px;font-weight:700;color:var(--green-700)">Rp {{ number_format($unit->harga_sewa,0,',','.') }}</div>
                            <div style="font-size:11px;color:#5a7a5a">/bulan</div>
                        </div>
                        <a href="{{ route('public.unit.detail', $unit) }}" style="background:var(--green-600);color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">Detail</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{ $units->withQueryString()->links() }}
        @endif
    </div>
</div>
@endsection
