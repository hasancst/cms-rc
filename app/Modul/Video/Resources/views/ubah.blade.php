@extends('admin.layout')

@section('judul', 'Ubah Video')

@section('konten')
<div class="card" style="max-width: 800px;">
    <div style="margin-bottom: 25px;">
        <h3>Ubah Video</h3>
    </div>

    <form action="/admin/video/perbarui/{{ $video->id }}" method="POST">
        @csrf
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Judul Video</label>
            <input type="text" name="judul" class="form-control" value="{{ $video->judul }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">URL Video</label>
            <input type="url" name="url" class="form-control" value="{{ $video->url }}" required>
            <small style="color: var(--text-muted); display: block; margin-top: 5px;">Masukkan link lengkap video (Youtube, Vimeo, dll).</small>
        </div>

        <div class="form-group">
            <label>Keterangan (Opsional)</label>
            <textarea name="keterangan" class="form-control" rows="3">{{ $video->keterangan }}</textarea>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ $video->aktif ? 'checked' : '' }}>
                <label for="aktif" style="margin-bottom: 0;">Aktifkan Video</label>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="unggulan" id="unggulan" value="1" {{ $video->unggulan ? 'checked' : '' }}>
                <label for="unggulan" style="margin-bottom: 0;">Jadikan Video Unggulan</label>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn">Perbarui Video</button>
            <a href="/admin/video" class="btn" style="background: #f1f5f9; color: #333;">Batal</a>
        </div>
    </form>
</div>
@endsection
