<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ASRI</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --green-50:   #f0faf4;
            --green-100:  #dcf4e7;
            --green-200:  #b8e9cf;
            --green-300:  #7ed0a8;
            --green-400:  #4ab882;
            --green-500:  #2a9d5c;
            --green-600:  #1e7c46;
            --green-700:  #175f36;
            --green-800:  #134b2b;
            --green-900:  #0e3820;
            --cream-50:   #fefdf8;
            --cream-100:  #fdf9ee;
            --cream-200:  #faf2d7;
            --cream-300:  #f5e8b8;
            --cream-400:  #edd98a;
            --sidebar-w:  260px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--cream-100);
            color: #1a2e1a;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: linear-gradient(180deg, var(--green-900) 0%, var(--green-800) 100%);
            z-index: 100;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo .brand {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--cream-200);
            letter-spacing: 1px;
        }

        .sidebar-logo .tagline {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            margin-top: 2px;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-user .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--green-600);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .sidebar-user .info .name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            line-height: 1;
        }

        .sidebar-user .info .role-badge {
            font-size: 10px;
            color: var(--green-300);
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 12px 0;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 20px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: rgba(255,255,255,0.65);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            margin: 1px 0;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.07);
            color: rgba(255,255,255,0.95);
            border-left-color: var(--green-400);
        }

        .nav-item.active {
            background: rgba(74, 184, 130, 0.15);
            color: var(--green-300);
            border-left-color: var(--green-400);
        }

        .nav-item i {
            font-size: 18px;
            width: 20px;
            flex-shrink: 0;
        }

        .nav-item .badge {
            margin-left: auto;
            background: var(--green-500);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #e8f0eb;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar .page-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--green-900);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notif-btn {
            position: relative;
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--green-50);
            border: 1px solid var(--green-200);
            display: flex; align-items: center; justify-content: center;
            color: var(--green-700);
            cursor: pointer;
            text-decoration: none;
            font-size: 18px;
            transition: all 0.2s;
        }

        .notif-btn:hover { background: var(--green-100); }

        .notif-btn .dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: #e53e3e;
            border-radius: 50%;
            border: 2px solid white;
        }

        .page-body {
            padding: 28px;
            flex: 1;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #e8f0eb;
            box-shadow: 0 1px 4px rgba(30, 124, 70, 0.06);
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--green-900);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 20px 24px;
            border: 1px solid #e8f0eb;
            box-shadow: 0 1px 4px rgba(30, 124, 70, 0.06);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.green   { background: var(--green-50);  color: var(--green-600); }
        .stat-icon.cream   { background: var(--cream-200); color: #8b6914; }
        .stat-icon.teal    { background: #e6f7f7;           color: #0c7b7b; }
        .stat-icon.orange  { background: #fff4ed;           color: #c84b00; }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--green-900);
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            color: #5a7a5a;
            margin-top: 3px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--green-600);
            color: white;
        }
        .btn-primary:hover { background: var(--green-700); }

        .btn-secondary {
            background: var(--cream-200);
            color: var(--green-800);
            border: 1px solid var(--cream-400);
        }
        .btn-secondary:hover { background: var(--cream-300); }

        .btn-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .btn-danger:hover { background: #fecaca; }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Table */
        .table-wrap {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #e8f0eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead th {
            background: var(--green-50);
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: var(--green-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--green-200);
        }

        tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid #f0f5f1;
            color: #2d3d2d;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--green-50); }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success  { background: #dcfce7; color: #166534; }
        .badge-warning  { background: #fef9c3; color: #854d0e; }
        .badge-danger   { background: #fee2e2; color: #991b1b; }
        .badge-info     { background: #dbeafe; color: #1e40af; }
        .badge-secondary{ background: #f3f4f6; color: #374151; }

        /* Forms */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--green-900);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #d1ddd3;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #1a2e1a;
            background: white;
            transition: border-color 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 3px rgba(42, 157, 92, 0.12);
        }

        select.form-control { cursor: pointer; }

        textarea.form-control { resize: vertical; min-height: 100px; }

        /* Alerts */
        .alert {
            padding: 13px 18px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        /* Grid helpers */
        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    @include('layouts.components.sidebar')

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        {{-- TOPBAR --}}
        <div class="topbar">
            <button id="sidebar-toggle" style="background:none;border:none;font-size:20px;color:var(--green-700);cursor:pointer;display:none">
                <i class="ri-menu-line"></i>
            </button>
            <span class="page-title">@yield('page-title', 'Dashboard')</span>

            <div class="topbar-right">
                {{-- Notifikasi --}}
                <a href="{{ route('notifikasi.index') }}" class="notif-btn">
                    <i class="ri-notification-3-line"></i>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="dot"></span>
                    @endif
                </a>

                {{-- User Dropdown --}}
                <div style="position:relative">
                    <button onclick="toggleDropdown()" style="display:flex;align-items:center;gap:8px;background:none;border:1px solid var(--green-200);border-radius:8px;padding:6px 12px;cursor:pointer;font-family:inherit">
                        <div style="width:28px;height:28px;background:var(--green-600);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span style="font-size:13px;font-weight:600;color:var(--green-900)">{{ auth()->user()->name }}</span>
                        <i class="ri-arrow-down-s-line" style="color:var(--green-600)"></i>
                    </button>
                    <div id="user-dropdown" style="display:none;position:absolute;right:0;top:calc(100%+8px);background:white;border:1px solid #e8f0eb;border-radius:10px;min-width:180px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:200;overflow:hidden">
                        {{-- Link ke halaman profil --}}
                        <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:8px;padding:11px 16px;color:#2d3d2d;font-size:14px;text-decoration:none;transition:background 0.15s" onmouseover="this.style.background='var(--green-50)'" onmouseout="this.style.background='white'">
                            <i class="ri-user-line"></i> Profil Saya
                        </a>                       
                        <hr style="border:none;border-top:1px solid #f0f5f1;margin:0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;display:flex;align-items:center;gap:8px;padding:11px 16px;color:#991b1b;font-size:14px;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left;transition:background 0.15s" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='white'">
                                <i class="ri-logout-box-line"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGE BODY --}}
        <div class="page-body">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success"><i class="ri-checkbox-circle-line"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="ri-close-circle-line"></i> {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const el = document.getElementById('user-dropdown');
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#user-dropdown') && !e.target.closest('[onclick="toggleDropdown()"]')) {
                const el = document.getElementById('user-dropdown');
                if (el) el.style.display = 'none';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
