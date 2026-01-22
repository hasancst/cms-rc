<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $pengaturan['nama_situs'] ?? 'iMakeCustom')</title>
    <meta name="description" content="@yield('description', $pengaturan['deskripsi_situs'] ?? 'Premium Bespoke Design & Manufacturing')">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('theme/imakecustom/css/style.css') }}">
    
    @yield('styles')
</head>
<body>
    <header id="header">
        <nav class="container">
            <div class="logo">
                <a href="/">{{ explode(' ', $pengaturan['nama_situs'] ?? 'iMake Custom')[0] }}<span>{{ explode(' ', $pengaturan['nama_situs'] ?? 'iMake Custom')[1] ?? '' }}</span></a>
            </div>
            <ul class="nav-links">
                <li><a href="/#features">Services</a></li>
                <li><a href="/#portfolio">Showcase</a></li>
                <li><a href="/#about">Innovation</a></li>
                @foreach($headerMenus as $m)
                    <li><a href="{{ $m->url }}">{{ $m->label }}</a></li>
                @endforeach
                <li><a href="/#contact" class="btn-primary">Get Started</a></li>
            </ul>
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <main>
        @yield('konten')
    </main>

    <footer>
        <div class="container footer-grid">
            <div class="footer-info">
                <div class="logo"><a href="/">iMake<span>Custom</span></a></div>
                <p>{{ $pengaturan['deskripsi_situs'] ?? 'Redefining custom manufacturing through innovation and precision.' }}</p>
            </div>
            <div class="footer-links">
                <h4>Company</h4>
                <ul>
                    <li><a href="/#about">Innovation</a></li>
                    @foreach($footerMenus as $fm)
                        <li><a href="{{ $fm->url }}">{{ $fm->label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="/privacy.html">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#">IN</a>
                    <a href="#">TW</a>
                    <a href="#">FB</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom container">
            <p>&copy; {{ date('Y') }} {{ $pengaturan['nama_situs'] ?? 'iMakeCustom' }}. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('theme/imakecustom/js/main.js') }}"></script>
    @yield('scripts')
    
    <script>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://help.imakecustom.com/app-assets/chat_js';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'best-support-system-chat'));
    </script>
</body>
</html>
