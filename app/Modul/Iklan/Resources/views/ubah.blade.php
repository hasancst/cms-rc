@extends('admin.layout')

@section('judul', 'Ubah Iklan')

@section('konten')
<div class="card">
    <form action="/admin/iklan/ubah/{{ $iklan->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Judul Iklan</label>
            <input type="text" name="judul" value="{{ $iklan->judul }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Posisi</label>
                <select name="posisi" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    @foreach(['header', 'sidebar_top', 'sidebar_bottom', 'article_middle', 'footer'] as $p)
                        <option value="{{ $p }}" {{ $iklan->posisi == $p ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $p)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Jenis Iklan</label>
                <select name="jenis" id="jenis-iklan" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <option value="gambar" {{ $iklan->jenis == 'gambar' ? 'selected' : '' }}>Gambar / Banner</option>
                    <option value="script" {{ $iklan->jenis == 'script' ? 'selected' : '' }}>Kode HTML / Script</option>
                </select>
            </div>
        </div>

        <div id="field-gambar" style="margin-bottom: 20px; display: {{ $iklan->jenis == 'gambar' ? 'block' : 'none' }};">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Upload Banner</label>
            @if($iklan->jenis == 'gambar')
                <div style="margin-bottom: 10px;">
                    <img src="/storage/{{ $iklan->konten }}" style="max-height: 100px; border-radius: 8px;">
                </div>
            @endif
            <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
            <small style="color: var(--text-muted);">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            
            <div style="margin-top: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Link Tujuan (Opsional)</label>
                <input type="url" name="link" value="{{ $iklan->link }}" placeholder="https://...">
            </div>
        </div>

        <div id="field-script" style="margin-bottom: 20px; display: {{ $iklan->jenis == 'script' ? 'block' : 'none' }};">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Kode Script (HTML/JS)</label>
            <textarea name="script" rows="6" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: monospace;">{{ $iklan->jenis == 'script' ? $iklan->konten : '' }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="aktif" value="1" {{ $iklan->aktif ? 'checked' : '' }}>
                <span style="font-weight: 600;">Aktifkan Iklan Ini</span>
            </label>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn"><i class="fas fa-save"></i> Perbarui Iklan</button>
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
