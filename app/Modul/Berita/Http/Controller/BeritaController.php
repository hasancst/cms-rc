<?php

namespace App\Modul\Berita\Http\Controller;

use App\Http\Controllers\Controller;
use App\Modul\Berita\Model\Berita;
use App\Modul\Berita\Model\Kategori;
use App\Modul\Berita\Model\Tag;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function indeks(Request $request)
    {
        $query = Berita::with(['penulis', 'kategoris', 'tags'])->latest();

        // Filter Pencarian
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->whereHas('kategoris', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori);
            });
        }

        $berita = $query->paginate(20)->withQueryString();
        $kategori = Kategori::all();

        return view('berita::indeks', compact('berita', 'kategori'));
    }

    public function tambah()
    {
        $kategori = Kategori::all();
        return view('berita::tambah', compact('kategori'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'isi' => 'required',
            'kategori_ids' => 'required|array',
            'gambar_utama' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar_utama')) {
            $media = new \App\Inti\MediaManager();
            $gambarPath = $media->upload($request->file('gambar_utama'), 'berita/sampul');
        }

        $berita = Berita::create([
            'judul' => $request->judul,
            'slug' => str()->slug($request->judul),
            'ringkasan' => $request->ringkasan,
            'isi' => $request->isi,
            'penulis_id' => auth()->id() ?: 1,
            'gambar_utama' => $gambarPath,
            'unggulan' => $request->has('unggulan'),
        ]);

        $berita->kategoris()->sync($request->kategori_ids);

        // Proses Tags
        if ($request->filled('tags')) {
            $tagNames = explode(',', $request->tags);
            $tagIds = [];
            foreach ($tagNames as $name) {
                $name = trim($name);
                if ($name) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => str()->slug($name)],
                        ['nama' => $name]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $berita->tags()->sync($tagIds);
        }

        return redirect('/admin/berita')->with('berhasil', 'Berita berhasil disimpan.');
    }

    public function ubah($id)
    {
        $berita = Berita::with(['kategoris', 'tags'])->findOrFail($id);
        $kategori = Kategori::all();
        return view('berita::ubah', compact('berita', 'kategori'));
    }

    public function perbarui(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        
        $request->validate([
            'judul' => 'required',
            'isi' => 'required',
            'kategori_ids' => 'required|array',
            'gambar_utama' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
        ]);

        $data = [
            'judul' => $request->judul,
            'slug' => str()->slug($request->judul),
            'ringkasan' => $request->ringkasan,
            'isi' => $request->isi,
            'unggulan' => $request->has('unggulan'),
        ];

        if ($request->hasFile('gambar_utama')) {
            $media = new \App\Inti\MediaManager();
            $data['gambar_utama'] = $media->upload($request->file('gambar_utama'), 'berita/sampul');
        }

        $berita->update($data);
        $berita->kategoris()->sync($request->kategori_ids);

        // Proses Tags
        if ($request->filled('tags')) {
            $tagNames = explode(',', $request->tags);
            $tagIds = [];
            foreach ($tagNames as $name) {
                $name = trim($name);
                if ($name) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => str()->slug($name)],
                        ['nama' => $name]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $berita->tags()->sync($tagIds);
        }

        return redirect('/admin/berita')->with('berhasil', 'Berita berhasil diperbarui.');
    }

    public function toggleUnggulan($id)
    {
        $berita = Berita::findOrFail($id);
        $berita->unggulan = !$berita->unggulan;
        $berita->save();

        return back()->with('berhasil', 'Status unggulan berhasil diperbarui.');
    }

    public function quickKategori(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        $berita->kategoris()->sync($request->kategori_ids);
        
        // Update the 'kategori' helper column as well
        $firstKategori = Kategori::whereIn('id', $request->kategori_ids)->first();
        if ($firstKategori) {
            $berita->kategori = $firstKategori->nama;
            $berita->save();
        }

        return back()->with('berhasil', 'Kategori berhasil diperbarui secara cepat.');
    }

    public function hapus($id)
    {
        $berita = Berita::findOrFail($id);
        $berita->delete();
        return redirect('/admin/berita')->with('berhasil', 'Berita berhasil dihapus.');
    }
}
