<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modul\Berita\Model\Berita;
use App\Modul\Artikel\Model\Artikel;

use App\Modul\Video\Model\Video;

class PublicController extends Controller
{
    public function index()
    {
        // Ambil data pengaturan
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();

        // Berita Unggulan (Featured)
        $unggulan = Berita::with(['kategoris', 'tags'])
            ->where('unggulan', true)
            ->latest()
            ->limit(3)
            ->get();

        // Jika tidak ada berita unggulan, ambil berita terbaru
        if ($unggulan->isEmpty()) {
            $unggulan = Berita::with(['kategoris', 'tags'])
                ->latest()
                ->limit(3)
                ->get();
        }

        // Berita Terbaru
        $beritaTerbaru = Berita::with(['kategoris', 'tags'])
            ->latest()
            ->paginate(10);

        // Sidebar: Berita Terpopuler (Sederhana ambil terbaru)
        $beritaPopuler = Berita::latest()->limit(5)->get();

        // Iklan Top Atas (jika ada modul iklan)
        $iklanTop = null;
        if (Schema::hasTable('iklan')) {
            $iklanTop = DB::table('iklan')
                ->where('posisi', 'sidebar_top')
                ->where('aktif', true)
                ->first();
        }

        // Video Terbaru (Ambil 2)
        $videoTerbaru = collect();
        if (Schema::hasTable('video')) {
            $videoTerbaru = Video::where('aktif', true)->orderBy('unggulan', 'desc')->latest()->take(2)->get();
        }

        return view('tema::index', compact('pengaturan', 'unggulan', 'beritaTerbaru', 'beritaPopuler', 'iklanTop', 'videoTerbaru'));
    }

    public function detailBerita($slug)
    {
        $berita = Berita::with(['kategoris', 'tags', 'penulis'])
            ->where('slug', $slug)
            ->firstOrFail();
            
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        $beritaTerkait = Berita::latest()->limit(3)->get();
        $topBerita = Berita::latest()->limit(5)->get();
        $latestVideos = \App\Modul\Video\Model\Video::where('aktif', true)->latest()->limit(3)->get();

        return view('tema::berita', compact('berita', 'pengaturan', 'beritaTerkait', 'topBerita', 'latestVideos'));
    }

    public function berita(Request $request)
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        $cari = $request->cari;
        
        $beritaList = Berita::with(['kategoris', 'tags', 'penulis'])
            ->when($cari, function($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                  ->orWhere('isi', 'like', "%{$cari}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $topBerita = Berita::latest()->limit(5)->get();
        return view('tema::semua_berita', compact('pengaturan', 'beritaList', 'topBerita', 'cari'));
    }

    public function kategoriBerita($slug)
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        
        $kategori = DB::table('kategori_berita')->where('slug', $slug)->first();
        if (!$kategori) abort(404);

        $beritaList = Berita::whereHas('kategoris', function($q) use ($kategori) {
                $q->where('kategori_berita.id', $kategori->id);
            })
            ->with(['kategoris', 'tags', 'penulis'])
            ->latest()
            ->paginate(12);

        $topBerita = Berita::latest()->limit(5)->get();
        return view('tema::semua_berita', compact('pengaturan', 'beritaList', 'topBerita', 'kategori'));
    }

    public function kategoriArtikel($slug)
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        
        $kategori = DB::table('kategori_artikel')->where('slug', $slug)->first();
        if (!$kategori) abort(404);

        $artikelList = Artikel::whereHas('kategori', function($q) use ($kategori) {
                $q->where('kategori_artikel.id', $kategori->id);
            })
            ->with(['kategori', 'penulis'])
            ->latest()
            ->paginate(12);
            
        // Reuse view structure or create new
        // Assuming we need a view for articles list too, similar to berita
        // For now, let's assume we might need `tema::semua_artikel` or just reuse layout if user hasn't asked for article page specifically yet, but the route exists.
        // Let's create a stub/reuse news view logic but for articles? No, variable names differ.
        // I'll leave this functional but I need to make sure the view exists or use a generic one.
        // The user asked specifically about "kategori berita frontend 404".
        
        return view('tema::semua_artikel', compact('pengaturan', 'artikelList', 'kategori'));
    }

    public function artikel()
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        
        $artikelList = Artikel::with(['penulis'])
            ->latest()
            ->paginate(12);

        $artikelPopuler = Artikel::latest()->limit(5)->get();

        return view('tema::semua_artikel', compact('pengaturan', 'artikelList', 'artikelPopuler'));
    }

    public function detailArtikel($slug)
    {
        $artikel = Artikel::with(['penulis'])
            ->where('slug', $slug)
            ->firstOrFail();
            
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        $artikelTerkait = Artikel::where('id', '!=', $artikel->id)->latest()->limit(3)->get();

        return view('tema::artikel', compact('artikel', 'pengaturan', 'artikelTerkait'));
    }

    public function video()
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        
        $videos = Video::where('aktif', true)
            ->orderBy('unggulan', 'desc')
            ->latest()
            ->paginate(12);

        return view('tema::video', compact('pengaturan', 'videos'));
    }

    public function detailVideo($slug)
    {
        $video = Video::where('slug', $slug)->where('aktif', true)->firstOrFail();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        $videoTerkait = Video::where('id', '!=', $video->id)->where('aktif', true)->latest()->limit(4)->get();

        return view('tema::detail_video', compact('video', 'pengaturan', 'videoTerkait'));
    }

    public function kontak()
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        return view('tema::kontak', compact('pengaturan'));
    }

    public function kirimKontak(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'perihal' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        DB::table('kontak')->insert([
            'nama' => $request->nama,
            'email' => $request->email,
            'perihal' => $request->perihal,
            'pesan' => $request->pesan,
            'status' => 'baru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('berhasil', 'Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.');
    }

    public function tentang()
    {
        $artikel = Artikel::where('slug', 'tentang-kami')->first();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        if ($artikel) {
            return view('tema::artikel_page', compact('artikel', 'pengaturan'));
        }
        return view('tema::tentang', compact('pengaturan'));
    }

    public function redaksi()
    {
        $artikel = Artikel::where('slug', 'redaksi')->first();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        if ($artikel) {
            return view('tema::artikel_page', compact('artikel', 'pengaturan'));
        }
        return view('tema::redaksi', compact('pengaturan'));
    }

    public function kebijakan()
    {
        $artikel = Artikel::where('slug', 'kebijakan-privasi')->first();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        if ($artikel) {
            return view('tema::artikel_page', compact('artikel', 'pengaturan'));
        }
        return view('tema::kebijakan', compact('pengaturan'));
    }

    public function syarat()
    {
        $artikel = Artikel::where('slug', 'syarat-ketentuan')->first();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        if ($artikel) {
            return view('tema::artikel_page', compact('artikel', 'pengaturan'));
        }
        return view('tema::syarat', compact('pengaturan'));
    }
}
