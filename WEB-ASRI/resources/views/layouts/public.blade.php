<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASRI - Rusun Jakarta')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green-50:  #f0faf4; --green-100: #dcf4e7; --green-200: #b8e9cf;
            --green-500: #2a9d5c; --green-600: #1e7c46; --green-700: #175f36;
            --green-800: #134b2b; --green-900: #0e3820;
            --cream-100: #fdf9ee; --cream-200: #faf2d7;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: white; color: #1a2e1a; }

        /* Public Header */
        .pub-header {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--green-100);
            padding: 0 5%;
        }
        .pub-header-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center;
            height: 68px; gap: 40px;
        }
        .pub-brand {
            font-family: 'Playfair Display', serif;
            font-size: 22px; font-weight: 700;
            color: var(--green-800);
            text-decoration: none;
        }
        .pub-nav { display: flex; gap: 6px; margin-left: auto; }
        .pub-nav a {
            padding: 8px 16px;
            font-size: 14px; font-weight: 500;
            color: var(--green-700);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .pub-nav a:hover { background: var(--green-50); }
        .pub-nav .btn-login {
            background: var(--green-700); color: white;
        }
        .pub-nav .btn-login:hover { background: var(--green-800); }

        /* Public Footer */
        .pub-footer {
            background: var(--green-900);
            color: rgba(255,255,255,0.75);
            padding: 48px 5% 24px;
            margin-top: 80px;
        }
        .pub-footer-inner { max-width: 1200px; margin: 0 auto; }
        .pub-footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        .pub-footer .brand {
            font-family: 'Playfair Display', serif;
            font-size: 22px; font-weight: 700;
            color: var(--cream-200);
            margin-bottom: 12px;
        }
        .pub-footer h4 {
            font-size: 13px; font-weight: 700;
            color: rgba(255,255,255,0.9);
            text-transform: uppercase; letter-spacing: 0.8px;
            margin-bottom: 14px;
        }
        .pub-footer a {
            display: block;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 8px;
            transition: color 0.2s;
        }
        .pub-footer a:hover { color: var(--green-300); }
        .pub-footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            text-align: center;
            font-size: 13px;
        }

        /* Container */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; }
    </style>
    @stack('styles')
</head>
<body>

{{-- PUBLIC HEADER --}}
<header class="pub-header">
    <div class="pub-header-inner">
        <a href="{{ route('home') }}" class="pub-brand">🌿 ASRI</a>

        <nav class="pub-nav">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('public.units') }}">Unit Rusun</a>
            <a href="{{ route('public.news') }}">Berita</a>

            @auth
                <a href="{{ route('dashboard') }}" class="btn-login">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}" class="btn-login">Daftar</a>
            @endauth
        </nav>
    </div>
</header>

{{-- PAGE CONTENT --}}
@yield('content')

{{-- PUBLIC FOOTER --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">
        <div class="pub-footer-grid">
            <div>
                <div class="brand">🌿 ASRI</div>
                <p style="font-size:14px;line-height:1.7;max-width:300px">
                    Sistem manajemen rusun digital terintegrasi untuk hunian nyaman di Jakarta.
                </p>
            </div>
            <div>
                <h4>Navigasi</h4>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('public.units') }}">Cari Unit</a>
                <a href="{{ route('public.news') }}">Berita</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
            <div>
                <h4>Kontak</h4>
                <a href="#">📞 (021) 1234-5678</a>
                <a href="#">✉️ info@asri.co.id</a>
                <a href="#">📍 Jakarta, Indonesia</a>
            </div>
        </div>
        <div class="pub-footer-bottom">
            © {{ date('Y') }} ASRI - Sistem Manajemen Rusun. All rights reserved.
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
