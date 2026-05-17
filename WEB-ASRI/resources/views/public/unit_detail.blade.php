@extends('layouts.public')
@section('title', $unit->nama_unit . ' - ASRI')

@section('content')
<div style="padding:48px 5%">
    <div class="container" style="padding:0">
        <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start">

            {{-- Kiri --}}
            <div>
                {{-- Gambar --}}
                <div style="height:380px;border-radius:16px;overflow:hidden;background:var(--green-100);margin-bottom:28px;display:flex;align-items:center;justify-content:center">
                    @if($unit->gambar)
                    <img src="{{ Storage::url($unit->gambar) }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                    <i class="ri-building-4-line" style="font-size:80px;color:var(--green-400)"></i>
                    @endif
                </div>

                <div style="margin-bottom:8px;display:flex;gap:10px;flex-wrap:wrap">
                    <span style="font-size:12px;background:var(--green-50);color:var(--green-700);padding:4px 12px;border-radius:12px;font-weight:600">{{ $unit->wilayah }}</span>
                    @if($unit->status==='tersedia')<span style="font-size:12px;background:#dcfce7;color:#166534;padding:4px 12px;border-radius:12px;font-weight:600">✓ Tersedia</span>@endif
                    @if($unit->luas_m2)<span style="font-size:12px;background:var(--cream-200);color:#78350f;padding:4px 12px;border-radius:12px;font-weight:600">{{ $unit->luas_m2 }} m²</span>@endif
                </div>

                <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--green-900);margin-bottom:6px">{{ $unit->nama_unit }}</h1>
                <p style="font-size:14px;color:#5a7a5a;margin-bottom:20px">{{ $unit->gedung }} · {{ $unit->alamat }}</p>

                @if($unit->deskripsi)
                <div style="font-size:15px;color:#2d3d2d;line-height:1.8;padding:18px;background:var(--cream-100);border-radius:10px;margin-bottom:20px">{{ $unit->deskripsi }}</div>
                @endif

                {{-- Unit Serupa --}}
                @if($unitSerupa->count())
                <div style="margin-top:32px">
                    <div style="font-size:16px;font-weight:700;color:var(--green-900);margin-bottom:16px">Unit Serupa di {{ $unit->wilayah }}</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
                        @foreach($unitSerupa as $s)
                        <a href="{{ route('public.unit.detail', $s) }}" style="text-decoration:none;border:1px solid #e8f0eb;border-radius:10px;overflow:hidden">
                            <div style="height:100px;background:var(--green-100);display:flex;align-items:center;justify-content:center">
                                @if($s->gambar)<img src="{{ Storage::url($s->gambar) }}" style="width:100%;height:100%;object-fit:cover">
                                @else<i class="ri-building-4-line" style="font-size:32px;color:var(--green-400)"></i>@endif
                            </div>
                            <div style="padding:10px">
                                <div style="font-size:13px;font-weight:700;color:var(--green-900)">{{ $s->nama_unit }}</div>
                                <div style="font-size:13px;font-weight:600;color:var(--green-700);margin-top:3px">Rp {{ number_format($s->harga_sewa,0,',','.') }}/bln</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Kanan - CTA --}}
            <div style="position:sticky;top:80px">
                <div style="background:white;border:2px solid var(--green-200);border-radius:16px;padding:28px;box-shadow:0 4px 16px rgba(30,124,70,0.1)">
                    <div style="font-size:32px;font-weight:700;color:var(--green-800);margin-bottom:4px">
                        Rp {{ number_format($unit->harga_sewa,0,',','.') }}
                    </div>
                    <div style="font-size:14px;color:#5a7a5a;margin-bottom:20px">/bulan</div>

                    <div style="display:grid;gap:10px;margin-bottom:20px">
                        @foreach([['Blok',$unit->blok],['Lantai',$unit->lantai],['No. Kamar',$unit->no_kamar],['Status',ucfirst($unit->status)]] as [$k,$v])
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f5f1">
                            <span style="font-size:13px;color:#5a7a5a">{{ $k }}</span>
                            <span style="font-size:13px;font-weight:600;color:var(--green-900)">{{ $v }}</span>
                        </div>
                        @endforeach
                    </div>

                    @if($unit->status === 'tersedia')
                        @auth
                            @if(auth()->user()->isCalonPenghuni())
                            <a href="{{ route('calon.pengajuan.create', $unit) }}" style="display:block;text-align:center;background:var(--green-700);color:white;padding:14px;border-radius:10px;font-size:15px;font-weight:700;text-decoration:none;margin-bottom:10px">
                                <i class="ri-file-list-3-line"></i> Ajukan Sewa Sekarang
                            </a>
                            @else
                            <div style="text-align:center;font-size:13px;color:#5a7a5a;padding:12px;background:var(--green-50);border-radius:8px">
                                Login sebagai Calon Penghuni untuk mengajukan sewa
                            </div>
                            @endif
                        @else
                        <a href="{{ route('login') }}" style="display:block;text-align:center;background:var(--green-700);color:white;padding:14px;border-radius:10px;font-size:15px;font-weight:700;text-decoration:none;margin-bottom:10px">
                            Login untuk Mengajukan Sewa
                        </a>
                        <a href="{{ route('register') }}" style="display:block;text-align:center;background:var(--cream-200);color:var(--green-800);padding:12px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none">
                            Daftar Akun Baru
                        </a>
                        @endauth
                    @else
                    <div style="text-align:center;padding:14px;background:#f3f4f6;border-radius:10px;color:#6b7280;font-size:14px;font-weight:600">
                        Unit Tidak Tersedia
                    </div>
                    @endif

                    <div style="margin-top:16px;text-align:center;font-size:12px;color:#5a7a5a">
                        <i class="ri-phone-line"></i> Hubungi: (021) 1234-5678
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
