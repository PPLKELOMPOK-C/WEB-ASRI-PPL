<div class="sidebar" id="sidebar">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="brand">🌿 ASRI</div>
        <div class="tagline">Sistem Manajemen Rusun</div>
    </div>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="info">
            <div class="name">{{ Str::limit(auth()->user()->name, 18) }}</div>
            <div class="role-badge">
                @if(auth()->user()->isAdmin()) Admin
                @elseif(auth()->user()->isPenghuni()) Penghuni
                @else Calon Penghuni
                @endif
            </div>
        </div>
    </div>

    {{-- Navigation per Role --}}
    <nav class="sidebar-nav">
        @if(auth()->user()->isAdmin())
            @include('layouts.components.sidebar-admin')
        @elseif(auth()->user()->isPenghuni())
            @include('layouts.components.sidebar-penghuni')
        @else
            @include('layouts.components.sidebar-calon')
        @endif
    </nav>
</div>
