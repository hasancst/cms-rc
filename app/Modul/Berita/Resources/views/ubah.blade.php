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
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Ringkasan</label>
                    <textarea name="ringkasan" rows="3" placeholder="Ringkasan singkat untuk mesin pencari & AI...">{{ $berita->ringkasan }}</textarea>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Isi Berita</label>
                    <textarea name="isi" id="editor" required>{{ $berita->isi }}</textarea>
                </div>
            </div>

            <div>

                


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
                    <h4 style="margin-bottom: 15px;">Tags</h4>
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
                // Removed hitungSeo
            }
        });


    });




</script>
@endsection
