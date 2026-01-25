@extends('tema::layout')

@section('title', $category->nama . ' - Knowledge Base')

@section('konten')
<section style="background: var(--purple-dark); padding: 150px 0 60px; color: #fff;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
            <a href="/kb" style="color: var(--primary); font-weight: 700;">Knowledge Base</a>
            <span style="opacity: 0.5;">/</span>
            <span>Kategori</span>
        </div>
        <h1 style="font-size: 40px; color: #fff;">{{ $category->nama }}</h1>
        <p style="opacity: 0.8; font-size: 18px;">{{ $category->deskripsi }}</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            @if($articles->isEmpty())
                <div style="padding: 100px 0; text-align: center;">
                    <i class="far fa-folder-open" style="font-size: 4rem; color: #e2e8f0; margin-bottom: 20px; display: block;"></i>
                    <p style="color: #64748b;">Belum ada artikel dalam kategori ini.</p>
                </div>
            @else
                <div style="display: grid; gap: 15px;">
                    @foreach($articles as $a)
                    <a href="/kb/{{ $a->slug }}" style="display: flex; align-items: center; gap: 20px; padding: 25px; background: #fff; border: 1px solid #e2e8f0; border-radius: 15px; text-decoration: none; transition: 0.3s;" class="article-link">
                        <div style="width: 45px; height: 45px; background: var(--bg-light); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="far fa-file-alt"></i>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="color: var(--purple-dark); margin: 0;">{{ $a->judul }}</h4>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #cbd5e1;"></i>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    .article-link:hover { border-color: var(--primary); transform: translateX(10px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .article-link:hover h4 { color: var(--primary); }
</style>
@endsection
