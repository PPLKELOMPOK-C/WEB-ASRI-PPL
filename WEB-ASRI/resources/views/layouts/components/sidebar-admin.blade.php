<div class="nav-section-label">Utama</div>

<a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="ri-dashboard-line"></i> Dashboard
</a>

<div class="nav-section-label">Manajemen</div>

<a href="{{ route('admin.unit.index') }}" class="nav-item {{ request()->routeIs('admin.unit.*') ? 'active' : '' }}">
    <i class="ri-building-line"></i> Unit Rusun
</a>

<a href="{{ route('admin.pengajuan.index') }}" class="nav-item {{ request()->routeIs('admin.pengajuan.*') ? 'active' : '' }}">
    <i class="ri-file-list-3-line"></i> Pengajuan Sewa
    @php $pendingCount = \App\Models\PengajuanSewa::where('status','pending')->count(); @endphp
    @if($pendingCount > 0)
        <span class="badge">{{ $pendingCount }}</span>
    @endif
</a>

{{-- Ganti admin.dokumen.index menjadi admin.penghuni.index --}}
<a href="{{ route('admin.penghuni.index') }}" class="nav-item {{ request()->routeIs('admin.penghuni.*') ? 'active' : '' }}">
    <i class="ri-group-line"></i> Data Penghuni
</a>


<a href="{{ route('admin.tagihan.index') }}" class="nav-item {{ request()->routeIs('admin.tagihan.*') ? 'active' : '' }}">
    <i class="ri-bill-line"></i> Tagihan
</a>


<a href="{{ route('admin.jadwal.index') }}" class="nav-item {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
    <i class="ri-calendar-event-line"></i> Jadwal Survei
</a>

<a href="{{ route('admin.laporan.index') }}" class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
    <i class="ri-tools-line"></i> Laporan Kerusakan
    @php $openCount = \App\Models\LaporanKerusakan::where('status','open')->count(); @endphp
    @if($openCount > 0)
        <span class="badge">{{ $openCount }}</span>
    @endif
</a>

<div class="nav-section-label">Konten</div>

<a href="{{ route('admin.berita.index') }}" class="nav-item {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
    <i class="ri-newspaper-line"></i> Berita & Info
</a>

<div class="nav-section-label">Laporan</div>

<a href="{{ route('admin.statistik') }}" class="nav-item {{ request()->routeIs('admin.statistik') ? 'active' : '' }}">
    <i class="ri-bar-chart-line"></i> Statistik
</a>
