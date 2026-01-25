@extends('admin.layout')

@section('judul', 'Tambah Berita Baru')

@section('konten')
<div class="card">
    <form action="/admin/berita/tambah" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px;">
            <!-- Kolom Utama -->
            <div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Judul Berita</label>
                    <input type="text" name="judul" required placeholder="Masukkan judul berita...">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Ringkasan</label>
                    <textarea name="ringkasan" rows="3" placeholder="Ringkasan singkat untuk tampilan kartu berita..."></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Isi Berita</label>
                    <textarea name="isi" id="summernote" required></textarea>
                </div>
            </div>

            <!-- Kolom Samping (Meta) -->
            <div>




                <!-- Gambar Utama -->
                <div style="margin-bottom: 25px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
                    <label style="display: block; margin-bottom: 12px; font-weight: 600;">Gambar Utama</label>
                    <div id="image-preview" style="width: 100%; height: 150px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; border: 2px dashed #cbd5e1; overflow: hidden;">
                        <i class="fas fa-image" style="font-size: 2rem; color: #94a3b8;"></i>
                    </div>
                    <input type="file" name="gambar_utama" id="image-input" accept="image/*" style="font-size: 0.8rem;">
                </div>

                <!-- Kategori -->
                <div style="margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label style="font-weight: 600;">Kategori</label>
                        <a href="/admin/berita/kategori" style="font-size: 0.8rem; color: var(--primary); text-decoration: none;"><i class="fas fa-plus-circle"></i> Kelola</a>
                    </div>
                    <div style="max-height: 150px; overflow-y: auto; background: #fff; padding: 12px; border-radius: 12px; border: 1px solid var(--border);">
                        @foreach($kategori as $k)
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.85rem; margin-bottom: 8px;">
                                <input type="checkbox" name="kategori_ids[]" value="{{ $k->id }}" style="width: auto !important;">
                                {{ $k->nama }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Tags -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Tags</label>
                    <input type="text" name="tags" placeholder="tag1, tag2, tag3" style="font-size: 0.85rem;">
                    <small style="color: var(--text-muted); font-size: 0.75rem; display: block; mt-1">Pisahkan dengan koma</small>
                </div>

                <!-- Unggulan -->
                <div style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px; background: var(--primary-light); padding: 15px; border-radius: 12px; border: 1px solid rgba(78, 115, 223, 0.1);">
                    <input type="checkbox" name="unggulan" id="unggulan" style="width: 20px; height: 20px; cursor: pointer;">
                    <label for="unggulan" style="font-weight: 600; cursor: pointer; color: var(--primary);">Jadikan Berita Unggulan</label>
                </div>




                <button type="submit" class="btn" style="width: 100%; justify-content: center; padding: 15px;">
                    <i class="fas fa-save"></i> Terbitkan Berita
                </button>
                <a href="/admin/berita" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('styles')
<style>
    .note-editor { background: #fff !important; border-radius: 12px !important; border: 1px solid var(--border) !important; }
    .note-editable { 
        background: #fff !important; 
        color: #333 !important; 
        font-family: 'Outfit', sans-serif !important;
        font-size: 1rem !important;
        line-height: 1.6 !important;
    }
    .note-toolbar { background: #f8fafc !important; border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; border-bottom: 1px solid var(--border) !important; }
    

</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
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
                // hitungSeo removed
            }
        });



        // Image Preview
        $('#image-input').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#image-preview').html(`<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`);
                }
                reader.readAsDataURL(file);
            }
        });




</script>
@endsection
