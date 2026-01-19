@extends('admin.layout')

@section('judul', 'Tambah Iklan')

@section('konten')
<div class="card">
    <form action="/admin/iklan/tambah" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Judul Iklan</label>
            <input type="text" name="judul" required placeholder="Contoh: Banner Sidebar Tokopedia">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Posisi</label>
                <select name="posisi" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <option value="header">Header (Atas)</option>
                    <option value="sidebar_top">Sidebar (Atas)</option>
                    <option value="sidebar_bottom">Sidebar (Bawah)</option>
                    <option value="article_middle">Tengah Artikel</option>
                    <option value="footer">Footer (Bawah)</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Jenis Iklan</label>
                <select name="jenis" id="jenis-iklan" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <option value="gambar">Gambar / Banner</option>
                    <option value="script">Kode HTML / Script (AdSense)</option>
                </select>
            </div>
        </div>

        <div id="field-gambar" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Upload Banner</label>
            <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
            
            <div style="margin-top: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Link Tujuan (Opsional)</label>
                <input type="url" name="link" placeholder="https://...">
            </div>
        </div>

        <div id="field-script" style="margin-bottom: 20px; display: none;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Kode Script (HTML/JS)</label>
            <textarea name="script" rows="6" placeholder="<script>...</script>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: monospace;"></textarea>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Iklan</button>
            <a href="/admin/iklan" class="btn" style="background: #94a3b8;">Batal</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('jenis-iklan').addEventListener('change', function() {
        if (this.value === 'gambar') {
            document.getElementById('field-gambar').style.display = 'block';
            document.getElementById('field-script').style.display = 'none';
        } else {
            document.getElementById('field-gambar').style.display = 'none';
            document.getElementById('field-script').style.display = 'block';
        }
    });
</script>
@endsection
