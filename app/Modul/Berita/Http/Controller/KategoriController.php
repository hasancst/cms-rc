<?php

namespace App\Modul\Berita\Http\Controller;

use App\Http\Controllers\Controller;
use App\Modul\Berita\Model\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function indeks()
    {
        $kategori = Kategori::latest()->get();
        return view('berita::kategori.indeks', compact('kategori'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:kategori_berita,nama',
        ]);

        Kategori::create([
            'nama' => $request->nama,
            'slug' => str()->slug($request->nama),
        ]);

        return back()->with('berhasil', 'Kategori baru berhasil ditambahkan.');
    }

    public function ubah($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('berita::kategori.ubah', compact('kategori'));
    }

    public function perbarui(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $request->validate([
            'nama' => 'required|unique:kategori_berita,nama,' . $id,
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'slug' => str()->slug($request->nama),
        ]);

        return redirect('/admin/berita/kategori')->with('berhasil', 'Kategori berhasil diperbarui.');
    }

    public function hapus($id)
    {
        Kategori::destroy($id);
        return back()->with('berhasil', 'Kategori berhasil dihapus.');
    }
}
