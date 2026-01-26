@extends('admin.layout')

@section('judul', 'Kategori Tiket')

@section('konten')
<div style="display: grid; grid-template-columns: 350px 1fr; gap: 25px;">
    <!-- Form Tambah/Edit -->
    <div class="card">
        <h3><i class="fas fa-plus-circle"></i> Tambah Kategori</h3>
        <form action="/admin/tiket/kategori" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Kategori</label>
                <input type="text" name="nama" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Induk Kategori (Opsional)</label>
                <select name="parent_id" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <option value="">-- Tanpa Induk (Kategori Utama) --</option>
                    @foreach($categories->where('parent_id', null) as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->nama }}</option>
                    @endforeach
                </select>
                <small style="color: #64748b;">Pilih induk jika ingin menjadikan ini sub-kategori.</small>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Deskripsi</label>
                <textarea name="deskripsi" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Urutan</label>
                <input type="number" name="urutan" value="0" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="aktif" checked value="1">
                    <span style="font-weight: 600;">Aktif</span>
                </label>
            </div>

            <button type="submit" class="btn" style="width: 100%;"><i class="fas fa-save"></i> Simpan Kategori</button>
        </form>
    </div>

    <!-- Daftar Kategori -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;"><i class="fas fa-list"></i> Daftar Kategori & Sub-Kategori</h3>
        </div>

        @if(session('berhasil'))
            <div style="background: #e6fffa; color: #234e52; padding: 15px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #b2f5ea;">
                {{ session('berhasil') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fff5f5; color: #c53030; padding: 15px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #feb2b2;">
                {{ session('error') }}
            </div>
        @endif

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px;">Nama</th>
                        <th style="padding: 12px;">Induk</th>
                        <th style="padding: 12px;">Urutan</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px;">
                            @if($cat->parent_id)
                                &nbsp;&nbsp;&nbsp; <i class="fas fa-level-up-alt fa-rotate-90" style="color: #cbd5e1;"></i>
                            @endif
                            <span style="font-weight: 600;">{{ $cat->nama }}</span>
                        </td>
                        <td style="padding: 12px;">
                            <span style="color: #64748b; font-size: 0.85rem;">{{ $cat->parent->nama ?? '-' }}</span>
                        </td>
                        <td style="padding: 12px;">{{ $cat->urutan }}</td>
                        <td style="padding: 12px;">
                            @if($cat->aktif)
                                <span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;">Aktif</span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;">Nonaktif</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <div style="display: flex; gap: 5px;">
                                <button onclick="editCategory({{ $cat->id }}, '{{ $cat->nama }}', '{{ $cat->parent_id }}', '{{ $cat->deskripsi }}', {{ $cat->urutan }}, {{ $cat->aktif }})" style="border: none; background: #eef2ff; color: #6366f1; width: 28px; height: 28px; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="/admin/tiket/kategori/hapus/{{ $cat->id }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    <button type="submit" style="border: none; background: #fef2f2; color: #ef4444; width: 28px; height: 28px; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 30px; text-align: center; color: #94a3b8;">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 400px; margin: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Edit Kategori</h3>
            <button onclick="document.getElementById('modalEdit').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form id="formEdit" action="" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nama Kategori</label>
                <input type="text" id="edit_nama" name="nama" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Induk Kategori (Opsional)</label>
                <select id="edit_parent_id" name="parent_id" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <option value="">-- Tanpa Induk (Kategori Utama) --</option>
                    @foreach($categories->where('parent_id', null) as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Deskripsi</label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Urutan</label>
                <input type="number" id="edit_urutan" name="urutan" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" id="edit_aktif" name="aktif" value="1">
                    <span style="font-weight: 600;">Aktif</span>
                </label>
            </div>

            <button type="submit" class="btn" style="width: 100%;"><i class="fas fa-save"></i> Perbarui Kategori</button>
        </form>
    </div>
</div>

<script>
function editCategory(id, nama, parentId, deskripsi, urutan, aktif) {
    const modal = document.getElementById('modalEdit');
    const form = document.getElementById('formEdit');
    
    form.action = '/admin/tiket/kategori/update/' + id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_parent_id').value = parentId || '';
    document.getElementById('edit_deskripsi').value = deskripsi || '';
    document.getElementById('edit_urutan').value = urutan || 0;
    document.getElementById('edit_aktif').checked = aktif == 1;
    
    modal.style.display = 'flex';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('modalEdit');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
@endsection
