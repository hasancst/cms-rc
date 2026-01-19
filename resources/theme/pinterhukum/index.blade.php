@extends('tema::layout')

@section('title', 'Pusat Edukasi Hukum Terpercaya')

@section('konten')

<!-- Mini Slider Section -->
<div class="container">
    <div class="mini-slider-wrapper">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @foreach($beritaTerbaru as $bs)
                    <div class="swiper-slide">
                        <a href="/berita/{{ $bs->slug }}">
                            @php
                                $imgUrl = $bs->gambar_utama;
                                if ($imgUrl && !str_starts_with($imgUrl, 'http')) {
                                    $imgUrl = '/storage/' . $imgUrl;
                                }
                                if (!$imgUrl) {
                                    $imgUrl = 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=800&q=80';
                                }
                            @endphp
                            <img src="{{ $imgUrl }}" class="slide-img" alt="{{ $bs->judul }}">
                            <div class="slide-content">
                                <span class="slide-cat">{{ $bs->kategoris->first()->nama ?? 'UMUM' }}</span>
                                <h4 class="slide-title">{{ $bs->judul }}</h4>
                                <div class="slide-date"><i class="far fa-clock"></i> {{ $bs->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</div>

<!-- Hero Section -->
<div class="container">
    <section class="hero">
        @if(isset($unggulan[0]))
        <a href="/berita/{{ $unggulan[0]->slug }}" class="hero-main hero-item">
            @php
                $imgUrl = $unggulan[0]->gambar_utama;
                if ($imgUrl && !str_starts_with($imgUrl, 'http')) {
                    $imgUrl = '/storage/' . $imgUrl;
                }
                if (!$imgUrl) {
                    $imgUrl = 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=800&q=80';
                }
            @endphp
            <img src="{{ $imgUrl }}" class="hero-img" alt="{{ $unggulan[0]->judul }}">
            <div class="hero-overlay">
                    @if($unggulan[0]->kategoris->first())
                        <span class="hero-tag">{{ $unggulan[0]->kategoris->first()->nama }}</span>
                    @else
                        <span class="hero-tag">BERITA</span>
                    @endif
                <h2 style="font-size: 2rem;">{{ $unggulan[0]->judul }}</h2>
                <div style="font-size: 0.85rem; margin-top: 10px; opacity: 0.9;">
                    <i class="far fa-clock"></i> {{ $unggulan[0]->created_at->format('d M Y') }} | <i class="far fa-user"></i> Admin
                </div>
            </div>
        </a>
        @endif

        <div class="hero-side">
            @foreach($unggulan->skip(1) as $u)
            <a href="/berita/{{ $u->slug }}" class="hero-main hero-item">
                @php
                    $imgUrl = $u->gambar_utama;
                    if ($imgUrl && !str_starts_with($imgUrl, 'http')) {
                        $imgUrl = '/storage/' . $imgUrl;
                    }
                    if (!$imgUrl) {
                        $imgUrl = 'https://images.unsplash.com/photo-1505664194779-8beaceb93744?auto=format&fit=crop&w=800&q=80';
                    }
                @endphp
                <img src="{{ $imgUrl }}" class="hero-img" alt="{{ $u->judul }}">
                <div class="hero-overlay" style="padding: 20px;">
                    @if($u->kategoris->first())
                        <span class="hero-tag" style="font-size: 0.65rem;">{{ $u->kategoris->first()->nama }}</span>
                    @else
                        <span class="hero-tag" style="font-size: 0.65rem;">HUKUM</span>
                    @endif
                    <h3 style="font-size: 1.1rem;">{{ $u->judul }}</h3>
                </div>
            </a>
            @endforeach
            
            @if($unggulan->count() < 3)
            <div class="hero-main" style="background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; padding: 30px; text-align: center;">
                <div>
                    <i class="fas fa-balance-scale" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h4>Edukasi Hukum Untuk Negeri</h4>
                    <p style="font-size: 0.8rem; margin-top: 10px; opacity: 0.8;">Pelajari aturan hukum dengan cara yang lebih mudah and menyenangkan.</p>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .mini-slider-wrapper {
        margin: 40px 0;
        position: relative;
    }
    .swiper {
        width: 100%;
        padding-bottom: 40px !important; 
        padding-top: 10px;
    }
    .swiper-slide {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.3s;
        height: auto;
        border: 1px solid var(--border);
    }
    .swiper-slide:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }
    .slide-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    .slide-content {
        padding: 15px;
    }
    .slide-cat {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--accent);
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }
    .slide-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.4;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8em;
    }
    .slide-date {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .swiper-button-next, .swiper-button-prev {
        color: var(--primary);
        background: #fff;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 14px;
        font-weight: bold;
    }
    .swiper-pagination-bullet-active {
        background: var(--primary);
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
        },
    });
</script>
@endsection

<!-- Main Content Area -->
<div class="container">
    <div class="main-content">
    
    <!-- Berita Terbaru -->
    <main>
        <h2 class="section-title">Berita Utama</h2>
        
        @forelse($beritaTerbaru as $b)
        @if($loop->iteration == 3 && isset($videoTerbaru) && $videoTerbaru)
        <div class="video-featured-card" style="background: #000; color: #fff; border-radius: 12px; margin-bottom: 30px; overflow: hidden;">
            <div style="padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; font-size: 1rem; color: #facc15;"><i class="fas fa-star"></i> Video Unggulan</h4>
                <span style="font-size: 0.75rem; opacity: 0.7;">Tonton Sekarang</span>
            </div>
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                @php
                    $url = $videoTerbaru->url;
                    $embedUrl = '';
                    
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                    } 
                    elseif (preg_match('/(?:vimeo\.com\/)([0-9]+)/', $url, $matches)) {
                        $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                    }
                @endphp

                @if($embedUrl)
                <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                        src="{{ $embedUrl }}" 
                        title="{{ $videoTerbaru->judul }}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen></iframe>
                @endif
            </div>
            <div style="padding: 15px 20px;">
                <h3 style="font-size: 1.1rem; margin-bottom: 5px; color: #fff;">{{ $videoTerbaru->judul }}</h3>
                @if($videoTerbaru->keterangan)
                <p style="font-size: 0.85rem; opacity: 0.8; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $videoTerbaru->keterangan }}
                </p>
                @endif
            </div>
        </div>
        @endif
        <article class="post-card">
            <a href="/berita/{{ $b->slug }}">
                @php
                    $imgUrl = $b->gambar_utama;
                    if ($imgUrl && !str_starts_with($imgUrl, 'http')) {
                        $imgUrl = '/storage/' . $imgUrl;
                    }
                    if (!$imgUrl) {
                        $imgUrl = 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=800&q=80';
                    }
                @endphp
                <img src="{{ $imgUrl }}" alt="{{ $b->judul }}">
            </a>
            <div class="post-info">
                <div class="post-date">
                    @if($b->kategoris->first())
                        <a href="/kategori-berita/{{ $b->kategoris->first()->slug }}" style="color: var(--primary); font-weight: 700;">{{ $b->kategoris->first()->nama }}</a> 
                    @else
                        <span style="color: var(--primary); font-weight: 700;">UMUM</span>
                    @endif
                    &bull; {{ $b->created_at->format('d F Y') }}
                </div>
                <h3 class="post-title"><a href="/berita/{{ $b->slug }}">{{ $b->judul }}</a></h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">
                    {{ str($b->ringkasan)->limit(120) }}
                </p>
                <div style="display: flex; gap: 5px;">
                    @foreach($b->tags->take(3) as $tag)
                        <a href="/tag/{{ $tag->slug }}" style="font-size: 0.7rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #64748b;">#{{ $tag->nama }}</a>
                    @endforeach
                </div>
            </div>
        </article>
        @empty
            <div style="text-align: center; padding: 50px; background: #fff; border-radius: 12px;">
                <p>Belum ada berita yang diterbitkan.</p>
            </div>
        @endforelse

        <div style="margin-top: 30px;">
            {{ $beritaTerbaru->links('vendor.pagination.theme') }}
        </div>
    </main>

    <!-- Sidebar -->
    <aside>
        <!-- Iklan Top Atas -->
        @if(isset($iklanTop) && $iklanTop)
            <div class="ad-zone" style="margin-bottom: 30px;">
                @if($iklanTop->jenis == 'gambar')
                    <a href="{{ $iklanTop->link ?? '#' }}" target="_blank">
                        <!-- Fix path to ensure it loads correctly -->
                        <img src="{{ str_starts_with($iklanTop->konten, 'http') ? $iklanTop->konten : '/storage/' . $iklanTop->konten }}" 
                             alt="{{ $iklanTop->judul }}" 
                             style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    </a>
                @else
                    <div style="width: 100%; overflow: hidden;">
                        {!! $iklanTop->konten !!}
                    </div>
                @endif
            </div>
        @endif
        <div class="widget">
            <h4 class="widget-title">Tentang Rumah Cyber</h4>
            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                {{ $pengaturan['deskripsi_situs'] ?? 'Rumah Cyber adalah platform informasi yang menyajikan konten edukatif, berita hukum terkini, dan analisis mendalam mengenai regulasi di Indonesia.' }}
            </p>
        </div>

        <div class="widget">
            <h4 class="widget-title">Berita Terpopuler</h4>
            <ul style="list-style: none;">
                @forelse($beritaPopuler as $b)
                <li style="margin-bottom: 20px; display: flex; gap: 12px; align-items: flex-start;">
                    <div style="width: 25px; height: 25px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 4px; flex-shrink: 0; font-weight: 700; font-size: 0.8rem;">
                        {{ $loop->iteration }}
                    </div>
                    <div>
                        <a href="/berita/{{ $b->slug }}" style="font-size: 0.9rem; font-weight: 600; line-height: 1.4; display: block;">{{ $b->judul }}</a>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">{{ $b->created_at->format('d M Y') }}</small>
                    </div>
                </li>
                @empty
                    <li>Belum ada berita.</li>
                @endforelse
            </ul>
        </div>



        <!-- Social Widget -->
        <div class="widget" style="background: var(--primary); color: #fff;">
            <h4 class="widget-title" style="border-color: rgba(255,255,255,0.2);">Ikuti Kami</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <a href="#" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-facebook-square"></i> Facebook
                </a>
                <a href="#" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
                <a href="#" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
                <a href="#" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-youtube"></i> YouTube
                </a>
            </div>
        </div>
    </aside>

    </div>
</div>

@endsection
