<?php

namespace App\Modul\Video\Http\Controller;

use App\Http\Controllers\Controller;
use App\Modul\Video\Model\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function indeks()
    {
        $video = Video::latest()->get();
        return view('video::indeks', compact('video'));
    }

    public function tambah()
    {
        return view('video::tambah');
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'url' => 'required|url',
        ]);

        $keterangan = $request->keterangan;
        
        // Auto-fill keterangan from Youtube (DISABLED - Use Client Side Fetch)
        // if (empty($keterangan) && preg_match('/(youtube\.com|youtu\.be)/', $request->url)) {
        //     $keterangan = $this->getYoutubeInfo($request->url) ?? 'Video dari Youtube';
        // }

        try {
            if ($request->has('unggulan')) {
                Video::where('unggulan', true)->update(['unggulan' => false]);
            }

            Video::create([
                'judul' => $request->judul,
                'url' => $request->url,
                'keterangan' => $keterangan,
                'aktif' => true,
                'unggulan' => $request->has('unggulan')
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Video Save Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['url' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }

        return redirect('/admin/video')->with('berhasil', 'Video berhasil ditambahkan.');
    }

    public function ubah($id)
    {
        $video = Video::findOrFail($id);
        return view('video::ubah', compact('video'));
    }

    public function perbarui(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        
        $request->validate([
            'judul' => 'required',
            'url' => 'required|url',
        ]);

        $keterangan = $request->keterangan;

        // Auto-fill keterangan from Youtube (DISABLED - Use Client Side Fetch)
        // if (empty($keterangan) && preg_match('/(youtube\.com|youtu\.be)/', $request->url)) {
        //     $keterangan = $this->getYoutubeInfo($request->url) ?? 'Video dari Youtube';
        // }

        if ($request->has('unggulan')) {
            Video::where('id', '!=', $id)->where('unggulan', true)->update(['unggulan' => false]);
        }

        $video->update([
            'judul' => $request->judul,
            'url' => $request->url,
            'keterangan' => $keterangan,
            'aktif' => $request->has('aktif'),
            'unggulan' => $request->has('unggulan')
        ]);

        return redirect('/admin/video')->with('berhasil', 'Video berhasil diperbarui.');
    }

    private function getYoutubeInfo($url)
    {
        try {
            $json = @file_get_contents('https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json');
            if ($json) {
                $data = json_decode($json, true);
                return $data['title'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    public function hapus($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();
        return redirect('/admin/video')->with('berhasil', 'Video berhasil dihapus.');
    }
}
