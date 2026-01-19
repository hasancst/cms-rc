<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Inti\ZipExtractor;

class AdminController extends Controller
{
    /**
     * Dashboard Utama
     */
    public function dashboard()
    {
        $data = [
            'jumlahModul' => DB::table('modul')->where('aktif', true)->count(),
            'jumlahTema' => DB::table('tema')->count(),
            'jumlahPengguna' => DB::table('pengguna')->count(),
            'jumlahArtikel' => DB::table('artikel')->count(),
            'jumlahBerita' => DB::table('berita')->count(),
        ];
        
        // Data peta pengunjung
        $visitorData = [];
        try {
            $visitorData = DB::table('stat_pengunjung')
                ->select('kode_negara', 'negara', DB::raw('count(*) as total'))
                ->groupBy('kode_negara', 'negara')
                ->orderByDesc('total')
                ->get();
        } catch (\Exception $e) {
            // Table might not exist yet
        }
        
        // Data Berita Populer
        $popularPages = [];
        try {
            $popularPages = DB::table('stat_pengunjung')
                ->select('url', DB::raw('count(*) as total'))
                ->where('url', 'like', '%/berita/%')
                ->groupBy('url')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    // Coba ambil judul berita dari slug di URL
                    $slug = basename($item->url);
                    $berita = DB::table('berita')->where('slug', $slug)->first();
                    $item->judul = $berita ? $berita->judul : $slug;
                    return $item;
                });
        } catch (\Exception $e) {
            // Ignore if table not found
        }
        
        return view('admin.dashboard', array_merge($data, [
            'visitorData' => $visitorData,
            'popularPages' => $popularPages
        ]));
    }

    /**
     * Manajemen Modul
     */
    public function indeksModul()
    {
        $folderModul = app_path('Modul');
        $directories = File::directories($folderModul);
        $modulTerpasang = DB::table('modul')->get()->keyBy('slug');

        $daftarModul = [];
        foreach ($directories as $dir) {
            $slug = basename($dir);
            $manifestPath = $dir . '/manifest.json';
            
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                $terpasang = isset($modulTerpasang[$slug]);
                
                $daftarModul[] = [
                    'nama' => $manifest['nama'] ?? $slug,
                    'slug' => $slug,
                    'versi' => $manifest['versi'] ?? '1.0.0',
                    'deskripsi' => $manifest['deskripsi'] ?? '',
                    'terpasang' => $terpasang,
                    'aktif' => $terpasang ? $modulTerpasang[$slug]->aktif : false,
                ];
            }
        }

        return view('admin.modul.indeks', ['modul' => $daftarModul]);
    }

    public function pasangModul(Request $request)
    {
        $slug = $request->slug;
        Artisan::call('modul:pasang', ['slug' => $slug]);
        return back()->with('berhasil', "Modul {$slug} berhasil dipasang.");
    }

    public function aktifkanModul(Request $request)
    {
        $slug = $request->slug;
        DB::table('modul')->where('slug', $slug)->update(['aktif' => true]);
        return back()->with('berhasil', "Modul {$slug} diaktifkan.");
    }

    public function nonaktifkanModul(Request $request)
    {
        $slug = $request->slug;
        Artisan::call('modul:nonaktifkan', ['slug' => $slug]);
        return back()->with('berhasil', "Modul {$slug} dinonaktifkan.");
    }

    public function copotModul(Request $request)
    {
        $slug = $request->slug;
        Artisan::call('modul:copot', ['slug' => $slug]);
        return back()->with('berhasil', "Modul {$slug} telah dicopot.");
    }

    public function unggahModul(Request $request)
    {
        $request->validate([
            'file_zip' => 'required|mimes:zip|max:10240',
        ]);

        $zipPath = $request->file('file_zip')->path();
        $extractor = new ZipExtractor();
        
        if ($extractor->validasiModul($zipPath)) {
            // Kita butuh nama folder utama di dalam zip untuk slug
            // Sederhananya kita pakai nama file zip tanpa ekstensi atau random jika tidak ada folder
            $slug = pathinfo($request->file('file_zip')->getClientOriginalName(), PATHINFO_FILENAME);
            $tujuan = app_path("Modul/{$slug}");
            
            $extractor->ekstrak($zipPath, $tujuan);
            return back()->with('berhasil', "Modul diunggah ke folder {$slug}. Silakan klik pasang.");
        }

        return back()->withErrors(['error' => 'ZIP tidak valid atau tidak memiliki manifest.json']);
    }

    /**
     * Manajemen Tema
     */
    public function indeksTema()
    {
        $folderTema = resource_path('theme');
        $directories = File::directories($folderTema);
        $temaTerdaftar = DB::table('tema')->get()->keyBy('slug');

        $daftarTema = [];
        foreach ($directories as $dir) {
            $slug = basename($dir);
            $manifestPath = $dir . '/theme.json';
            
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                
                $daftarTema[] = [
                    'nama' => $manifest['nama'] ?? $slug,
                    'slug' => $slug,
                    'versi' => $manifest['versi'] ?? '1.0.0',
                    'aktif' => isset($temaTerdaftar[$slug]) ? $temaTerdaftar[$slug]->aktif : false,
                ];
            }
        }

        return view('admin.tema.indeks', ['tema' => $daftarTema]);
    }

    public function aktifkanTema(Request $request)
    {
        $slug = $request->slug;
        
        // Pastikan terdaftar di DB
        DB::table('tema')->updateOrInsert(
            ['slug' => $slug],
            [
                'nama' => $slug,
                'versi' => '1.0.0',
                'aktif' => false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $themeManager = new \App\Inti\ThemeManager();
        $themeManager->setTema($slug);

        return back()->with('berhasil', "Tema {$slug} aktif.");
    }

    public function perbaruiTema(Request $request)
    {
        $slug = $request->slug;
        $nama = $request->nama;

        // Update di Database
        DB::table('tema')->where('slug', $slug)->update(['nama' => $nama, 'updated_at' => now()]);

        // Update di theme.json
        $manifestPath = resource_path("theme/{$slug}/theme.json");
        if (File::exists($manifestPath)) {
            $manifest = json_decode(File::get($manifestPath), true);
            $manifest['nama'] = $nama;
            File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        }

        return back()->with('berhasil', "Nama tema berhasil diperbarui.");
    }

    /**
     * Manajemen Pengaturan
     */
    public function indeksPengaturan()
    {
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        return view('admin.pengaturan', compact('pengaturan'));
    }

    public function simpanPengaturan(Request $request)
    {
        $data = $request->except('_token', 'logo');
        
        // Handle Upload Logo
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $media = new \App\Inti\MediaManager();
            $path = $media->upload($request->file('logo'), 'situs'); // Upload ke folder situs
             
            DB::table('pengaturan')->updateOrInsert(
                ['kunci' => 'logo'],
                ['nilai' => $path, 'updated_at' => now()]
            );
        }

        foreach ($data as $kunci => $nilai) {
            DB::table('pengaturan')->updateOrInsert(
                ['kunci' => $kunci],
                ['nilai' => $nilai, 'updated_at' => now()]
            );
        }

        // Sinkronisasi Redis ke .env
        if (isset($data['optimasi_redis_aktif'])) {
            $driver = $data['optimasi_redis_aktif'] == '1' ? 'redis' : 'database';
            $this->updateEnv('CACHE_STORE', $driver);
            $this->updateEnv('CACHE_DRIVER', $driver);
            
            if (isset($data['optimasi_redis_host'])) {
                $this->updateEnv('REDIS_HOST', $data['optimasi_redis_host']);
            }
            if (isset($data['optimasi_redis_port'])) {
                $this->updateEnv('REDIS_PORT', $data['optimasi_redis_port']);
            }
            if (isset($data['optimasi_redis_password'])) {
                $password = $data['optimasi_redis_password'] ?: 'null';
                $this->updateEnv('REDIS_PASSWORD', $password);
            }
        }

        return back()->with('berhasil', 'Pengaturan sistem berhasil diperbarui.');
    }

    public function unggahMedia(Request $request)
    {
        if ($request->hasFile('image')) {
            $media = new \App\Inti\MediaManager();
            $path = $media->upload($request->file('image'), 'berita');
            return response()->json(['url' => '/storage/' . $path]);
        }
        return response()->json(['error' => 'Gagal mengunggah gambar.'], 400);
    }

    /**
     * Helper untuk update file .env
     */
    protected function updateEnv($key, $value)
    {
        $path = base_path('.env');
        if (File::exists($path)) {
            $content = File::get($path);
            $oldValue = env($key);

            // Jika key sudah ada, ganti nilainya
            if (preg_match("/^{$key}=(.*)$/m", $content)) {
                $content = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $content);
            } else {
                // Jika belum ada, tambahkan di akhir
                $content .= "\n{$key}={$value}";
            }

            File::put($path, $content);
            // Refresh config cache jika diperlukan
            // Artisan::call('config:clear');
        }
    }
}
