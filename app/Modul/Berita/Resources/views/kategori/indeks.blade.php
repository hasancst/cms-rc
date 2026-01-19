@extends('admin.layout')

@section('judul', 'Kategori Berita')

@section('konten')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
    <!-- Form Tambah -->
    <div class="card">
        <h3>Tambah Kategori</h3>
        <form action="/admin/berita/kategori" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Kategori</label>
                <input type="text" name="nama" required placeholder="Contoh: Politik">
            </div>
            <button type="submit" class="btn" style="width: 100%;">Simpan Kategori</button>
        </form>
    </div>

    <!-- Tabel Daftar -->
    <div class="card">
        <h3>Daftar Kategori</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Jumlah Berita</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $k)
                <tr>
                    <td><strong>{{ $k->nama }}</strong></td>
                    <td><code>{{ $k->slug }}</code></td>
                    <td>{{ $k->berita_count ?? 0 }}</td>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            <a href="/admin/berita/kategori/ubah/{{ $k->id }}" style="color: var(--primary);">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/admin/berita/kategori/hapus/{{ $k->id }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; padding:0;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Belum ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
