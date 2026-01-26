<?php

namespace App\Modul\Tiket\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modul\Tiket\Model\TiketKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TiketKategoriController extends Controller
{
    public function index()
    {
        $categories = TiketKategori::with('parent')->orderBy('urutan')->get();
        return view('tiket::admin.category_index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:tiket_categories,id',
        ]);

        TiketKategori::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'parent_id' => $request->parent_id,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->urutan ?? 0,
            'aktif' => $request->has('aktif') ? true : false,
        ]);

        return back()->with('berhasil', 'Kategori tiket berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $cat = TiketKategori::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:tiket_categories,id',
        ]);

        // Prevent category from being its own parent
        if ($request->parent_id == $id) {
            return back()->with('error', 'Kategori tidak dapat menjadi induk dari dirinya sendiri.');
        }
        
        // Prevent circular reference
        if ($request->parent_id) {
            $checkParent = TiketKategori::find($request->parent_id);
            while ($checkParent) {
                if ($checkParent->parent_id == $id) {
                    return back()->with('error', 'Tidak dapat membuat circular reference.');
                }
                $checkParent = $checkParent->parent;
            }
        }
        
        $cat->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'parent_id' => $request->parent_id,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->urutan ?? 0,
            'aktif' => $request->has('aktif') ? true : false,
        ]);

        return back()->with('berhasil', 'Kategori tiket berhasil diperbarui.');
    }

    public function delete($id)
    {
        TiketKategori::findOrFail($id)->delete();
        return back()->with('berhasil', 'Kategori tiket berhasil dihapus.');
    }
}
