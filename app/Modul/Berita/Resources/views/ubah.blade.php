@extends('admin.layout')

@section('judul', 'Ubah Berita')

@section('konten')
<div class="card">
    <form action="/admin/berita/ubah/{{ $berita->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div>
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Judul Berita</label>
                    <input type="text" name="judul" value="{{ $berita->judul }}" required placeholder="Masukkan judul menarik...">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Ringkasan (AEO/GEO Optimized)</label>
                    <textarea name="ringkasan" rows="3" placeholder="Ringkasan singkat untuk mesin pencari & AI...">{{ $berita->ringkasan }}</textarea>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Isi Berita</label>
                    <textarea name="isi" id="editor" required>{{ $berita->isi }}</textarea>
                </div>
            </div>

            <div>
                <div class="card" style="margin-bottom: 25px; background: #fdf2f2; border-left: 4px solid #ef4444;">
                    <h4 style="margin-bottom: 10px;"><i class="fas fa-search"></i> SEO Score</h4>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div id="seo-score-circle" style="width: 55px; height: 55px; border-radius: 50%; border: 5px solid #ef4444; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; color: #ef4444;">
                            0
                        </div>
                        <div>
                            <div id="seo-status" style="font-weight: 600; font-size: 0.85rem;">Perlu Optimasi</div>
                            <small style="color: var(--text-muted); display: block; font-size: 0.75rem;" id="seo-tips">Lengkapi konten Anda...</small>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 25px; background: #f8fafc;">
                    <h4 style="margin-bottom: 15px;">Publikasi</h4>
                    <div style="margin-bottom: 15px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="unggulan" value="1" {{ $berita->unggulan ? 'checked' : '' }} style="width: auto;">
                            <span>Berita Unggulan</span>
                        </label>
                    </div>
                    <button type="submit" class="btn" style="width: 100%; justify-content: center;">
                        <i class="fas fa-save"></i> Perbarui Berita
                    </button>
                    <a href="/admin/berita" class="btn" style="width: 100%; justify-content: center; margin-top: 10px; background: #94a3b8;">Batal</a>
                </div>

                <div class="card" style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px;">Pilih Kategori</h4>
                    <div style="max-height: 200px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                        @foreach($kategori as $kat)
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="kategori_ids[]" value="{{ $kat->id }}" 
                                    {{ $berita->kategoris->contains($kat->id) ? 'checked' : '' }} style="width: auto;">
                                <span>{{ $kat->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="card" style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px;">Gambar Utama</h4>
                    @if($berita->gambar_utama)
                        @php
                            $imgUrl = $berita->gambar_utama;
                            if ($imgUrl && !str_starts_with($imgUrl, 'http')) {
                                $imgUrl = '/storage/' . $imgUrl;
                            }
                        @endphp
                        <img src="{{ $imgUrl }}" style="width: 100%; border-radius: 8px; margin-bottom: 10px;">
                    @endif
                    <input type="file" name="gambar_utama" accept="image/*">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">Format: JPG, PNG. Maks: 2MB. Kosongkan jika tidak ingin mengubah.</p>
                </div>

                <div class="card">
                    <h4 style="margin-bottom: 15px;">Tags (LLMO)</h4>
                    <input type="text" name="tags" value="{{ $berita->tags->pluck('nama')->implode(', ') }}" placeholder="Pisahkan dengan koma...">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">Penting untuk pemetaan entitas oleh mesin AI.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('styles')
<style>
    .note-editable {
        font-family: 'Outfit', sans-serif !important;
        font-size: 1rem !important;
        line-height: 1.6 !important;
        color: #2d3748 !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#editor').summernote({
            placeholder: 'Tulis isi berita di sini...',
            tabsize: 2,
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents, $editable) {
                    hitungSeo();
                }
            }
        });

        // Live calculation
        $('input[name="judul"], textarea[name="ringkasan"], input[name="tags"]').on('input change', function() {
            hitungSeo();
        });

        hitungSeo(); // Start up
    });

    function hitungSeo() {
        let score = 0;
        let tips = [];
        
        let judul = $('input[name="judul"]').val() || '';
        let ringkasan = $('textarea[name="ringkasan"]').val() || '';
        let isi = $('#editor').summernote('code').replace(/<[^>]*>/g, '') || '';
        
        // Title Score (25%)
        if (judul.length >= 40 && judul.length <= 70) score += 25;
        else if (judul.length > 0) { score += 10; tips.push("Judul idealnya 40-70 karakter."); }

        // Ringkasan Score (25%) - AEO
        if (ringkasan.length >= 120 && ringkasan.length <= 160) score += 25;
        else if (ringkasan.length > 0) { score += 10; tips.push("Ringkasan (AEO) idealnya 120-160 karakter."); }

        // Content Length (30%) - GEO
        let wordCount = isi.trim().split(/\s+/).filter(w => w.length > 0).length;
        if (wordCount >= 300) score += 30;
        else if (wordCount > 50) { score += 15; tips.push("Konten (GEO) minimal 300 kata."); }

        // LLMO Tags (20%)
        let tags = $('input[name="tags"]').val() || '';
        let tagCount = tags.split(',').filter(t => t.trim().length > 0).length;
        if (tagCount >= 3) score += 20;
        else if (tagCount > 0) { score += 10; tips.push("Gunakan min. 3 tags untuk LLMO."); }

        // Update UI
        score = Math.min(score, 100);
        $('#seo-score-circle').text(score);
        
        let color = "#ef4444";
        let status = "Perlu Optimasi";
        if (score > 80) { color = "#22c55e"; status = "Sangat Baik"; }
        else if (score > 50) { color = "#f59e0b"; status = "Cukup Baik"; }
        
        $('#seo-score-circle').css({'border-color': color, 'color': color});
        $('#seo-status').text(status).css('color', color);
        $('#seo-tips').text(tips.length > 0 ? tips[0] : "Konten sudah sangat baik!");
    }
</script>
@endsection
