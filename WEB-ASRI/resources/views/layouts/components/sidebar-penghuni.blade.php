<div class="nav-section-label">Utama</div>

<a href="{{ route('penghuni.dashboard') }}" class="nav-item {{ request()->routeIs('penghuni.dashboard') ? 'active' : '' }}">
    <i class="ri-dashboard-line"></i> Dashboard
</a>

<div class="nav-section-label">Hunian</div>

<a href="{{ route('penghuni.tagihan.index') }}" class="nav-item {{ request()->routeIs('penghuni.tagihan.*') ? 'active' : '' }}">
    <i class="ri-bill-line"></i> Tagihan Saya
    @php
        $belumBayar = auth()->user()->tagihans()->where('status','belum_bayar')->count();
    @endphp
    @if($belumBayar > 0)
        <span class="badge">{{ $belumBayar }}</span>
    @endif
</a>

<a href="{{ route('penghuni.laporan.index') }}" class="nav-item {{ request()->routeIs('penghuni.laporan.*') ? 'active' : '' }}">
    <i class="ri-tools-line"></i> Laporan Kerusakan
</a>

<div class="nav-section-label">Informasi</div>

<a href="{{ route('notifikasi.index') }}" class="nav-item {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
    <i class="ri-notification-3-line"></i> Notifikasi
</a>

<a href="{{ route('public.news') }}" class="nav-item">
    <i class="ri-newspaper-line"></i> Berita
</a>
