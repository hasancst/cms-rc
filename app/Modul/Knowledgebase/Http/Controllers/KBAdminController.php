<?php

namespace App\Modul\Knowledgebase\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modul\Knowledgebase\Model\KBCategory;
use App\Modul\Knowledgebase\Model\KBArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KBAdminController extends Controller
{
    // Category Management
    public function indexCategory()
    {
        $categories = KBCategory::orderBy('urutan')->get();
        return view('knowledgebase::admin.category_index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['nama' => 'required']);
        KBCategory::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'ikon' => $request->ikon,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->urutan ?? 0
        ]);
        return back()->with('berhasil', 'Kategori KB berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = KBCategory::findOrFail($id);
        $cat->update($request->all());
        return back()->with('berhasil', 'Kategori KB berhasil diperbarui.');
    }

    public function deleteCategory($id)
    {
        KBCategory::findOrFail($id)->delete();
        return back()->with('berhasil', 'Kategori KB berhasil dihapus.');
    }

    // Article Management
    public function indexArticle()
    {
        $articles = KBArticle::with('category')->latest()->paginate(20);
        $categories = KBCategory::all();
        return view('knowledgebase::admin.article_index', compact('articles', 'categories'));
    }

    public function createArticle()
    {
        $categories = KBCategory::all();
        if($categories->isEmpty()) return redirect('/admin/kb/category')->with('error', 'Buat kategori terlebih dahulu.');
        return view('knowledgebase::admin.article_create', compact('categories'));
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'category_id' => 'required',
            'konten' => 'required'
        ]);

        KBArticle::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul),
            'category_id' => $request->category_id,
            'konten' => $request->konten,
            'aktif' => $request->has('aktif'),
            'urutan' => $request->urutan ?? 0,
            'tags' => $request->tags
        ]);

        return redirect('/admin/kb/article')->with('berhasil', 'Artikel KB berhasil disimpan.');
    }

    public function editArticle($id)
    {
        $article = KBArticle::findOrFail($id);
        $categories = KBCategory::all();
        return view('knowledgebase::admin.article_edit', compact('article', 'categories'));
    }

    public function updateArticle(Request $request, $id)
    {
        $article = KBArticle::findOrFail($id);
        $article->update([
            'judul' => $request->judul,
            'category_id' => $request->category_id,
            'konten' => $request->konten,
            'aktif' => $request->has('aktif'),
            'urutan' => $request->urutan ?? 0,
            'tags' => $request->tags
        ]);
        return redirect('/admin/kb/article')->with('berhasil', 'Artikel KB berhasil diperbarui.');
    }

    public function deleteArticle($id)
    {
        KBArticle::findOrFail($id)->delete();
        return back()->with('berhasil', 'Artikel KB berhasil dihapus.');
    }
}
