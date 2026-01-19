<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pengaturan['nama_situs'] ?? 'CMS' }} - @yield('judul')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css" rel="stylesheet">
    
    <!-- Summernote CSS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

    <style>
        :root {
            --primary: #4e73df;
            --primary-light: #f8faff;
            --bg-body: #f4f7fe;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --accent: #ebf1ff;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --border: #edf2f7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: sticky;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 10;
        }

        .sidebar-logo {
            padding: 30px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 15px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-item.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border);
        }

        /* Main Content */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .header {
            height: 80px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-brand .dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: right;
        }

        .header-user .info h4 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .header-user .info span {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .content {
            padding: 40px;
        }

        /* UI Elements */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .card h3 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background-color: #ffffff !important;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .badge {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            background: var(--accent);
            color: var(--primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            background: #dcfce7;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Layout Grid */
        .grid-dashboard {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-info .label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-info .value {
            font-size: 1.5rem;
            font-weight: 700;
        }
    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-rocket"></i> {{ $pengaturan['nama_situs'] ?? 'CMS' }}
        </div>
        
        <nav class="sidebar-nav">
            <a href="/admin" class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            @if(in_array('statistik', array_map('strtolower', $modulAktif)))
            <a href="/admin/statistik" class="nav-item {{ request()->is('admin/statistik*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Statistik
            </a>
            @endif
            @if(in_array('artikel', array_map('strtolower', $modulAktif)))
            <a href="/admin/artikel" class="nav-item {{ request()->is('admin/artikel*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Artikel
            </a>
            @endif
            @if(in_array('berita', array_map('strtolower', $modulAktif)))
            <a href="/admin/berita" class="nav-item {{ request()->is('admin/berita*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Berita
            </a>
            @endif
            @if(in_array('iklan', array_map('strtolower', $modulAktif)))
            <a href="/admin/iklan" class="nav-item {{ request()->is('admin/iklan*') ? 'active' : '' }}">
                <i class="fas fa-ad"></i> Iklan
            </a>
            @endif
            @if(in_array('video', array_map('strtolower', $modulAktif)))
            <a href="/admin/video" class="nav-item {{ request()->is('admin/video*') ? 'active' : '' }}">
                <i class="fas fa-video"></i> Video
            </a>
            @endif
            <a href="/admin/modul" class="nav-item {{ request()->is('admin/modul*') ? 'active' : '' }}">
                <i class="fas fa-cubes"></i> Modul
            </a>
            @if(in_array('kontak', array_map('strtolower', $modulAktif)))
            <a href="/admin/kontak" class="nav-item {{ request()->is('admin/kontak*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Pesan Kontak
            </a>
            @endif
            @if(in_array('komentar', array_map('strtolower', $modulAktif)))
            <a href="/admin/komentar" class="nav-item {{ request()->is('admin/komentar*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Komentar
            </a>
            @endif
            @if(in_array('menu', array_map('strtolower', $modulAktif)))
            <a href="/admin/menu" class="nav-item {{ request()->is('admin/menu*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Menu
            </a>
            @endif
            <a href="/admin/tema" class="nav-item {{ request()->is('admin/tema*') ? 'active' : '' }}">
                <i class="fas fa-palette"></i> Tema
            </a>
            <a href="/admin/pengguna" class="nav-item {{ request()->is('admin/pengguna*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Pengguna
            </a>
            <a href="/admin/pengaturan" class="nav-item {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="/keluar" method="POST" style="width: 100%;">
                @csrf
                <button type="submit" class="nav-item" style="color: #ef4444; width: 100%; border: none; background: none; text-align: left; font-family: inherit; font-size: inherit; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="header">
            <div class="header-brand">
                <div class="dot"></div>
                <nav style="font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <span style="color: var(--text-muted);">Admin</span>
                    @php 
                        $segments = request()->segments();
                        $breadcrumb = '';
                    @endphp
                    @foreach($segments as $segment)
                        @if($segment != 'admin')
                            <i class="fas fa-chevron-right" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                            <span style="color: var(--text-main); font-weight: 600;">{{ ucfirst($segment) }}</span>
                        @endif
                    @endforeach
                </nav>
            </div>

            <div class="header-user">
                <div class="info">
                    <h4>Hi {{ auth()->user()?->nama ?? 'Administrator' }}</h4>
                    <span>{{ auth()->user()?->email }}</span>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->nama ?? 'Administrator') }}&background=4e73df&color=fff" alt="Avatar" style="width: 45px; height: 45px; border-radius: 12px;">
            </div>
        </header>

        <section class="content">
            @if(session('berhasil'))
                <div class="alert">
                    <i class="fas fa-check-circle"></i> {{ session('berhasil') }}
                </div>
            @endif

            <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 700;">@yield('judul')</h1>
                    <p style="color: var(--text-muted);">Selamat pagi, {{ auth()->user()?->nama ?? 'Administrator' }}. Berikut ringkasan sistem hari ini.</p>
                </div>
                <div style="background: var(--primary); color: #ffffff; padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                    <i class="far fa-clock"></i>
                    <span id="realtime-clock" style="font-weight: 600;">{{ now()->format('H.i.s') }}</span>
                </div>
            </div>

            @yield('konten')
        </section>
    </main>

    <script>
        // Jam Realtime
        const clockElement = document.getElementById('realtime-clock');
        if (clockElement) {
            setInterval(() => {
                const now = new Date();
                const jam = String(now.getHours()).padStart(2, '0');
                const menit = String(now.getMinutes()).padStart(2, '0');
                const detik = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${jam}.${menit}.${detik}`;
            }, 1000);
        }
    </script>
    @yield('scripts')
</body>
</html>
