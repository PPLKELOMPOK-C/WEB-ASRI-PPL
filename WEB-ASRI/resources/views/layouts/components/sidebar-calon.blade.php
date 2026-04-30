<div class="nav-section-label">Utama</div>

<a href="{{ route('calon.dashboard') }}" class="nav-item {{ request()->routeIs('calon.dashboard') ? 'active' : '' }}">
    <i class="ri-dashboard-line"></i> Dashboard
</a>

<div class="nav-section-label">Sewa</div>

<a href="{{ route('public.units') }}" class="nav-item">
    <i class="ri-search-line"></i> Cari Unit
</a>

<a href="{{ route('calon.pengajuan.index') }}" class="nav-item {{ request()->routeIs('calon.pengajuan.*') ? 'active' : '' }}">
    <i class="ri-file-list-3-line"></i> Pengajuan Saya
</a>

<div class="nav-section-label">Informasi</div>

<a href="{{ route('notifikasi.index') }}" class="nav-item {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
    <i class="ri-notification-3-line"></i> Notifikasi
</a>

<a href="{{ route('public.news') }}" class="nav-item">
    <i class="ri-newspaper-line"></i> Berita
</a>
