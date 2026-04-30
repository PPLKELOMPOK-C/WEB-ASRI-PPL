@extends('layouts.public')
@section('title','ASRI - Hunian Nyaman di Jakarta')

@section('content')

{{-- Hero Section --}}
<section style="background:linear-gradient(135deg,var(--green-900) 0%,var(--green-700) 60%,var(--green-500) 100%);padding:90px 5%;min-height:520px;display:flex;align-items:center;position:relative;overflow:hidden">
    <div style="position:absolute;right:-80px;top:-80px;width:500px;height:500px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>
    <div style="position:absolute;right:120px;bottom:-100px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,0.03)"></div>
    <div class="container" style="padding:0;position:relative;z-index:1">
        <div style="max-width:600px">
            <div style="font-size:12px;font-weight:700;color:var(--green-300);text-transform:uppercase;letter-spacing:2px;margin-bottom:14px">🌿 Rusun Digital Jakarta</div>
            <h1 style="font-family:'Playfair Display',serif;font-size:50px;font-weight:700;color:white;line-height:1.18;margin-bottom:18px">
                Hunian Nyaman,<br>Proses Mudah
            </h1>
            <p style="font-size:17px;color:rgba(255,255,255,0.8);line-height:1.75;margin-bottom:36px;max-width:480px">
                Temukan unit rusun impian Anda di Jakarta. Pendaftaran 100% online, transparan, dan cepat.
            </p>
            <div style="display:flex;gap:14px;flex-wrap:wrap">
                <a href="{{ route('public.units') }}" style="display:inline-flex;align-items:center;gap:9px;background:white;color:var(--green-800);padding:14px 28px;border-radius:10px;font-weight:700;font-size:15px;text-decoration:none;transition:transform 0.2s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <i class="ri-search-line"></i> Cari Unit Tersedia
                </a>
                @guest
                <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:9px;background:var(--green-400);color:white;padding:14px 28px;border-radius:10px;font-weight:700;font-size:15px;text-decoration:none">
                    Daftar Sekarang
                </a>
                @endguest
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section style="background:white;border-bottom:1px solid var(--green-100);padding:28px 5%">
    <div class="container" style="padding:0;display:grid;grid-template-columns:repeat(4,1fr);gap:0;text-align:center">
        @foreach([['ri-building-line',$stats['total_unit'],'Total Unit'],['ri-home-smile-line',$stats['tersedia'],'Unit Tersedia'],['ri-team-line',$stats['total_penghuni'],'Penghuni Aktif'],['ri-map-pin-line',5,'Wilayah Jakarta']] as [$ic,$v,$l])
        <div style="padding:16px;border-right:1px solid #f0f5f1">
            <i class="{{ $ic }}" style="font-size:26px;color:var(--green-500);margin-bottom:6px;display:block"></i>
            <div style="font-size:28px;font-weight:700;color:var(--green-900);line-height:1">{{ $v }}</div>
            <div style="font-size:13px;color:#5a7a5a;margin-top:3px">{{ $l }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- Unit Tersedia --}}
<section style="padding:72px 5%">
    <div class="container" style="padding:0">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:36px">
            <div>
                <div style="font-size:12px;font-weight:700;color:var(--green-500);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:6px">Pilihan Terbaik</div>
                <h2 style="font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:var(--green-900)">Unit Tersedia</h2>
            </div>
            <a href="{{ route('public.units') }}" style="color:var(--green-600);font-weight:600;font-size:14px;text-decoration:none">Lihat Semua →</a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
            @forelse($unitTersedia as $unit)
            <div style="background:white;border-radius:14px;overflow:hidden;border:1px solid var(--green-100);box-shadow:0 2px 8px rgba(30,124,70,0.06);transition:all 0.25s" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 12px 28px rgba(30,124,70,0.13)'" onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(30,124,70,0.06)'">
                <div style="height:190px;background:linear-gradient(135deg,var(--green-100),var(--green-200));overflow:hidden;position:relative">
                    @if($unit->gambar)
                    <img src="{{ Storage::url($unit->gambar) }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center"><i class="ri-building-4-line" style="font-size:52px;color:var(--green-400)"></i></div>
                    @endif
                    <div style="position:absolute;top:10px;right:10px;background:#dcfce7;color:#166534;font-size:11px;font-weight:700;padding:4px 10px;border-radius:12px">Tersedia</div>
                </div>
                <div style="padding:18px">
                    <div style="font-size:11px;font-weight:700;color:var(--green-600);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">{{ $unit->wilayah }}</div>
                    <h3 style="font-size:16px;font-weight:700;color:var(--green-900);margin-bottom:4px">{{ $unit->nama_unit }}</h3>
                    <p style="font-size:13px;color:#5a7a5a;margin-bottom:14px">{{ $unit->gedung }}</p>
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <div style="font-size:20px;font-weight:700;color:var(--green-700)">Rp {{ number_format($unit->harga_sewa,0,',','.') }}</div>
                            <div style="font-size:11px;color:#5a7a5a">/bulan</div>
                        </div>
                        <a href="{{ route('public.unit.detail', $unit) }}" style="background:var(--green-600);color:white;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">Lihat →</a>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:40px;color:#5a7a5a">Belum ada unit tersedia.</div>
            @endforelse
        </div>
    </div>
</section>

{{-- Cara Daftar --}}
<section style="background:var(--cream-100);padding:72px 5%">
    <div class="container" style="padding:0">
        <div style="text-align:center;margin-bottom:48px">
            <h2 style="font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:var(--green-900)">Cara Mendaftar</h2>
            <p style="font-size:15px;color:#5a7a5a;margin-top:8px">Proses mudah, cepat, dan transparan</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:28px">
            @foreach([['ri-user-add-line','1','Buat Akun','Daftar akun sebagai Calon Penghuni'],['ri-search-line','2','Pilih Unit','Cari & pilih unit rusun sesuai kebutuhan'],['ri-file-list-3-line','3','Ajukan Sewa','Isi form & upload dokumen persyaratan'],['ri-home-smile-line','4','Tinggal','Setelah verifikasi, mulai huni unit Anda']] as [$ic,$n,$t,$d])
            <div style="text-align:center">
                <div style="width:64px;height:64px;background:var(--green-700);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                    <i class="{{ $ic }}" style="font-size:28px;color:white"></i>
                </div>
                <div style="font-size:12px;font-weight:700;color:var(--green-500);margin-bottom:4px">LANGKAH {{ $n }}</div>
                <div style="font-size:15px;font-weight:700;color:var(--green-900);margin-bottom:6px">{{ $t }}</div>
                <div style="font-size:13px;color:#5a7a5a;line-height:1.6">{{ $d }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Berita --}}
@if($beritaTerbaru->count())
<section style="padding:72px 5%">
    <div class="container" style="padding:0">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:36px">
            <h2 style="font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:var(--green-900)">Berita Terbaru</h2>
            <a href="{{ route('public.news') }}" style="color:var(--green-600);font-weight:600;font-size:14px;text-decoration:none">Semua Berita →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
            @foreach($beritaTerbaru as $n)
            <a href="{{ route('public.news.detail', $n->slug) }}" style="text-decoration:none;background:white;border:1px solid #e8f0eb;border-radius:14px;overflow:hidden;display:block;transition:box-shadow 0.2s" onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.09)'" onmouseout="this.style.boxShadow='none'">
                <div style="height:160px;background:var(--green-100);display:flex;align-items:center;justify-content:center;overflow:hidden">
                    @if($n->gambar_cover)
                    <img src="{{ Storage::url($n->gambar_cover) }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                    <i class="ri-newspaper-line" style="font-size:40px;color:var(--green-400)"></i>
                    @endif
                </div>
                <div style="padding:16px">
                    <div style="font-size:11px;color:var(--green-500);font-weight:600;margin-bottom:6px">{{ $n->published_at->format('d M Y') }}</div>
                    <div style="font-size:15px;font-weight:700;color:var(--green-900);line-height:1.4">{{ Str::limit($n->judul, 65) }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
