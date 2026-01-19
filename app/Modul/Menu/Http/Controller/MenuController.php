<?php

namespace App\Modul\Menu\Http\Controller;

use App\Http\Controllers\Controller;
use App\Modul\Menu\Model\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function indeks()
    {
        $headerMenus = Menu::with('children')
            ->whereNull('parent_id')
            ->where('posisi', 'header')
            ->orderBy('urutan')
            ->get();

        $footerMenus = Menu::with('children')
            ->whereNull('parent_id')
            ->where('posisi', 'footer')
            ->orderBy('urutan')
            ->get();

        // Ambil kategori untuk pilihan menu dinamis
        $kategoriBerita = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('kategori_berita')) {
            $kategoriBerita = \Illuminate\Support\Facades\DB::table('kategori_berita')->select('id', 'nama', 'slug')->get();
        }

        $kategoriArtikel = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('kategori_artikel')) {
            $kategoriArtikel = \Illuminate\Support\Facades\DB::table('kategori_artikel')->select('id', 'nama', 'slug')->get();
        }

        return view('menu::indeks', compact('headerMenus', 'footerMenus', 'kategoriBerita', 'kategoriArtikel'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'url' => 'required',
        ]);

        Menu::create([
            'label' => $request->label,
            'url' => $request->url,
            'parent_id' => $request->parent_id,
            'target' => $request->target ?? '_self',
            'posisi' => $request->posisi ?? 'header',
            'urutan' => Menu::where('parent_id', $request->parent_id)->count() + 1
        ]);

        return back()->with('berhasil', 'Menu berhasil ditambahkan.');
    }

    public function perbarui(Request $request, $id)
    {
        $request->validate([
            'label' => 'required',
            'url' => 'required',
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update($request->all());

        return back()->with('berhasil', 'Menu berhasil diperbarui.');
    }

    public function hapus($id)
    {
        Menu::destroy($id);
        return back()->with('berhasil', 'Menu berhasil dihapus.');
    }

    public function urukan(Request $request)
    {
        // Logika sederhana untuk urutan (bisa ditingkatkan nanti)
        foreach ($request->urutan as $id => $order) {
            Menu::where('id', $id)->update(['urutan' => $order]);
        }
        return back()->with('berhasil', 'Urutan menu diperbarui.');
    }
}
