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
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Deskripsi (Otomatis muncul di frontend)</label>
                <textarea name="deskripsi" rows="3" placeholder="Tuliskan deskripsi singkat mengenai kategori ini..."></textarea>
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
                    <th>Berita</th>
                    <th>Jadikan Menu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $k)
                @php
                    $urlCheck = '/berita/kategori/' . $k->slug;
                    $statusHeader = false;
                    $statusFooter = false;
                    
                    if (isset($menuKategori[$urlCheck])) {
                        $positions = $menuKategori[$urlCheck]->pluck('posisi')->toArray();
                        $statusHeader = in_array('header', $positions);
                        $statusFooter = in_array('footer', $positions);
                    }
                @endphp
                <tr>
                    <td>
                        <strong>{{ $k->nama }}</strong>
                        <div style="margin-top: 5px; display: flex; gap: 5px;">
                            @if($statusHeader)
                                <span class="badge" style="background: #ebf1ff; color: #4e73df; font-size: 0.65rem;" title="Ada di Antarmuka Atas">MAIN MENU</span>
                            @endif
                            @if($statusFooter)
                                <span class="badge" style="background: #fff7ed; color: #c2410c; font-size: 0.65rem;" title="Ada di Tautan Cepat">FOOTER</span>
                            @endif
                        </div>
                    </td>
                    <td><code>{{ $k->slug }}</code></td>
                    <td>{{ $k->berita_count ?? 0 }}</td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            @if(!$statusHeader)
                            <form action="/admin/berita/kategori/ke-menu/{{ $k->id }}" method="POST">
                                @csrf
                                <input type="hidden" name="posisi" value="header">
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 0.75rem; background: var(--primary);" title="Tambah ke Main Menu">
                                    <i class="fas fa-heading"></i> Main Menu
                                </button>
                            </form>
                            @else
                                <button class="btn" style="padding: 5px 10px; font-size: 0.75rem; background: #d1d5db; color: #9ca3af; cursor: not-allowed;" disabled>
                                    <i class="fas fa-check"></i> Menu Header
                                </button>
                            @endif

                            @if(!$statusFooter)
                            <form action="/admin/berita/kategori/ke-menu/{{ $k->id }}" method="POST">
                                @csrf
                                <input type="hidden" name="posisi" value="footer">
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 0.75rem; background: #64748b;" title="Tambah ke Footer">
                                    <i class="fas fa-shoe-prints"></i> Footer
                                </button>
                            </form>
                            @else
                                <button class="btn" style="padding: 5px 10px; font-size: 0.75rem; background: #d1d5db; color: #9ca3af; cursor: not-allowed;" disabled>
                                    <i class="fas fa-check"></i> Menu Footer
                                </button>
                            @endif
                        </div>
                    </td>
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
                    <td colspan="5" style="text-align: center;">Belum ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
