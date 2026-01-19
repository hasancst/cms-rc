@extends('tema::layout')

@section('title', isset($kategori) ? 'Berita ' . $kategori->nama : 'Berita Terbaru')

@section('konten')
<!-- Category Hero Section - Full Width -->
@if(isset($kategori))
<div class="category-hero" style="background: linear-gradient(135deg, #014A7A 0%, #002D4B 100%); padding: 100px 0; margin-bottom: 50px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
    
    <div class="container" style="text-align: center; position: relative; z-index: 1;">
        <nav style="font-size: 0.9rem; margin-bottom: 20px;">
            <a href="/" style="color: rgba(255,255,255,0.8); font-weight: 600;">Beranda</a> 
            <span style="color: rgba(255,255,255,0.4); margin: 0 15px;">/</span>
            <a href="/berita" style="color: rgba(255,255,255,0.8); font-weight: 600;">Berita</a>
            <span style="color: rgba(255,255,255,0.4); margin: 0 15px;">/</span>
            <span style="color: #fff; font-weight: 700;">{{ $kategori->nama }}</span>
        </nav>
        <h1 style="color: #fff; font-size: 4rem; font-weight: 800; margin-bottom: 15px; letter-spacing: -2px;">{{ $kategori->nama }}</h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.25rem; max-width: 700px; margin: 0 auto; line-height: 1.6; font-weight: 500;">Kumpulan informasi dan berita hukum terbaru serta terpercaya seputar {{ strtolower($kategori->nama) }} di Indonesia.</p>
    </div>
</div>
@elseif(isset($cari))
<div class="category-hero" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); padding: 100px 0; margin-bottom: 50px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    <div style="position: absolute; top: -100px; left: -100px; width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
    <div class="container" style="text-align: center; position: relative; z-index: 1;">
        <h1 style="color: #fff; font-size: 3.5rem; font-weight: 800; margin-bottom: 15px; letter-spacing: -1px;">Hasil Pencarian</h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.5rem; max-width: 700px; margin: 0 auto; line-height: 1.6; font-weight: 500;">
            Ditemukan {{ $beritaList->total() + (isset($featured) ? 1 : 0) }} hasil untuk kata kunci: <span style="color: #fbbf24; text-decoration: underline;">"{{ $cari }}"</span>
        </p>
    </div>
</div>
@endif

<div class="container">
<div class="main-content">
    <main>
        @if($beritaList->count() > 0)
            <!-- Featured Post in Category (First Item) -->
            @php $featured = $beritaList->shift(); @endphp
            <div class="featured-card-premium" style="margin-bottom: 40px; position: relative; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); background: #fff;">
                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 0;">
                    <div style="height: 400px; overflow: hidden; position: relative;">
                         <img src="{{ $featured->gambar_utama ? (str_starts_with($featured->gambar_utama, 'http') ? $featured->gambar_utama : asset('storage/' . $featured->gambar_utama)) : asset('theme/pinterhukum/img/default.jpg') }}" 
                              style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;" 
                              alt="{{ $featured->judul }}"
                              class="featured-img-hover">
                         <div style="position: absolute; top: 20px; left: 20px; background: var(--primary); color: #fff; padding: 5px 15px; border-radius: 4px; font-weight: 700; font-size: 0.75rem; letter-spacing: 1px;">TERBARU</div>
                    </div>
                    <div style="padding: 40px; display: flex; flex-direction: column; justify-content: center;">
                        <div style="color: var(--primary); font-weight: 700; font-size: 0.85rem; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 2px;">
                            {{ $featured->kategoris->first()->nama ?? 'BERITA' }}
                        </div>
                        <h2 style="font-size: 2.2rem; line-height: 1.2; margin-bottom: 20px; font-weight: 800;">
                            <a href="/berita/{{ $featured->slug }}" style="color: var(--text-main); text-decoration: none;">{{ $featured->judul }}</a>
                        </h2>
                        <p style="color: var(--text-muted); line-height: 1.7; margin-bottom: 25px; font-size: 1.05rem;">
                            {{ Str::limit($featured->ringkasan, 150) }}
                        </p>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($featured->penulis->nama ?? 'A') }}&background=014A7A&color=fff" style="width: 40px; height: 40px; border-radius: 50%;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.9rem;">{{ $featured->penulis->nama ?? 'Administrator' }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $featured->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid for Remaining Posts -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
                @foreach($beritaList as $item)
                    <article class="premium-card">
                        <div class="card-image-box">
                            <a href="/berita/{{ $item->slug }}">
                                <img src="{{ $item->gambar_utama ? (str_starts_with($item->gambar_utama, 'http') ? $item->gambar_utama : asset('storage/' . $item->gambar_utama)) : asset('theme/pinterhukum/img/default.jpg') }}" alt="{{ $item->judul }}">
                            </a>
                        </div>
                        <div class="card-body-box">
                            <div class="tag-meta">{{ $item->created_at->format('d M Y') }}</div>
                            <h3 class="card-title-box"><a href="/berita/{{ $item->slug }}">{{ $item->judul }}</a></h3>
                            <p class="card-excerpt-box">{{ Str::limit($item->ringkasan, 100) }}</p>
                            <div class="card-footer-box">
                                <span><i class="far fa-user"></i> {{ $item->penulis->nama ?? 'Admin' }}</span>
                                <a href="/berita/{{ $item->slug }}" class="read-more-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 50px;">
                {{ $beritaList->links('vendor.pagination.theme') }}
            </div>
        @else
            <div style="text-align: center; padding: 100px 40px; background: #fff; border-radius: 20px; box-shadow: var(--shadow); border: 1px solid #e2e8f0;">
                <div style="width: 120px; height: 120px; background: #fdf2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                    <i class="fas fa-search" style="font-size: 3.5rem; color: #ef4444;"></i>
                </div>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 15px;">{{ isset($cari) ? 'Pencarian Tidak Ditemukan' : 'Belum Ada Berita' }}</h2>
                <p style="color: #64748b; font-size: 1.1rem; max-width: 500px; margin: 0 auto 30px; line-height: 1.6;">
                    {{ isset($cari) ? "Maaf, kami tidak dapat menemukan berita yang cocok dengan kata kunci \"$cari\". Silakan coba menggunakan kata kunci lain." : "Sepertinya belum ada berita yang diterbitkan di kategori ini. Silakan kembali lagi nanti." }}
                </p>
                <a href="/berita" class="btn-premium" style="display: inline-block; padding: 15px 35px; background: var(--primary); color: #fff; text-decoration: none; border-radius: 50px; font-weight: 700; transition: all 0.3s; box-shadow: 0 10px 20px rgba(78, 115, 223, 0.2);">
                    Lihat Semua Berita
                </a>
            </div>
        @endif

    </main>

    <aside>
        <div class="widget">
            <h4 class="widget-title">Artikel Populer</h4>
            <ul class="popular-list">
                @foreach($artikelPopuler as $idx => $pop)
                <li class="popular-item">
                    <span class="number">{{ $idx + 1 }}</span>
                    <div>
                        <a href="/artikel/{{ $pop->slug }}" class="popular-link">{{ $pop->judul }}</a>
                        <small style="color: var(--text-muted);">{{ $pop->created_at->format('d M Y') }}</small>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        
        <div class="widget">
            <h4 class="widget-title">Berlangganan</h4>
            <div style="padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid var(--border);">
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">Dapatkan pembaruan berita hukum terbaru langsung di email Anda.</p>
                <form onsubmit="event.preventDefault(); alert('Terima kasih telah berlangganan!');">
                    <input type="email" placeholder="Email Anda" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <button class="btn" style="width: 100%; justify-content: center;">Berlangganan</button>
                </form>
            </div>
        </div>

        <div class="widget" style="position: sticky; top: 100px;">
            <h4 class="widget-title">Sponsor</h4>
            <div style="border-radius: 12px; overflow: hidden; box-shadow: var(--shadow);">
                <img src="{{ asset('theme/pinterhukum/img/ad-side.jpg') }}" onerror="this.src='https://placehold.co/300x600?text=Iklan+Premium'" style="width: 100%; display: block;">
            </div>
        </div>
    </aside>

</div>
</div>

<style>
    /* Premium Redesign Styles */
    .premium-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .premium-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .card-image-box {
        height: 220px;
        overflow: hidden;
    }

    .card-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .premium-card:hover .card-image-box img {
        transform: scale(1.1);
    }

    .card-body-box {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .tag-meta {
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card-title-box {
        font-size: 1.3rem;
        margin-bottom: 15px;
        line-height: 1.4;
        font-weight: 700;
    }

    .card-title-box a {
        color: var(--text-main);
        text-decoration: none;
        transition: color 0.2s;
    }

    .card-title-box a:hover {
        color: var(--primary);
    }

    .card-excerpt-box {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .card-footer-box {
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #64748b;
    }

    .read-more-link {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .read-more-link:hover {
        text-decoration: underline;
    }

    .featured-card-premium:hover .featured-img-hover {
        transform: scale(1.05);
    }

    @media (max-width: 991px) {
        .featured-card-premium { grid-template-columns: 1fr !important; }
        .featured-card-premium > div { grid-template-columns: 1fr !important; }
        .featured-card-premium img { height: 250px !important; }
    }

    @media (max-width: 768px) {
        .main-content { grid-template-columns: 1fr !important; }
        .grid-layout { grid-template-columns: 1fr !important; }
        div[style*="grid-template-columns: repeat(2, 1fr)"] { grid-template-columns: 1fr !important; }
        .category-hero h1 { font-size: 2.2rem !important; }
    }
</style>
@endsection
