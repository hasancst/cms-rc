<?php

namespace App\Modul\Tiket\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modul\Tiket\Model\Tiket;
use App\Modul\Tiket\Model\TiketPesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiketController extends Controller
{
    public function indeks()
    {
        $tikets = Tiket::orderBy('created_at', 'desc')->paginate(20);
        return view('tiket::indeks', compact('tikets'));
    }

    public function tambah()
    {
        return view('tiket::tambah');
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'required',
            'prioritas' => 'required|in:rendah,sedang,tinggi',
            'kategori' => 'required',
        ]);

        $noTiket = 'TKT-' . strtoupper(substr(uniqid(), 7));

        $tiket = Tiket::create([
            'no_tiket' => $noTiket,
            'judul' => $request->judul,
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'kategori' => $request->kategori,
            'prioritas' => $request->prioritas,
            'status' => 'terbuka',
            'pesan_awal' => $request->pesan,
        ]);

        return redirect('/admin/tiket/detail/' . $tiket->id)->with('berhasil', 'Tiket baru berhasil dibuat.');
    }

    public function detail($id)
    {
        $tiket = Tiket::with(['pesans' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('tiket::detail', compact('tiket'));
    }

    public function balas(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required',
        ]);

        $tiket = Tiket::findOrFail($id);

        TiketPesan::create([
            'tiket_id' => $tiket->id,
            'user_id' => Auth::id(),
            'nama_pengirim' => Auth::user()->nama,
            'pesan' => $request->pesan,
            'is_admin' => true,
        ]);

        // Jika status selesai, buka kembali jika dibalas? 
        // Biasanya kalau admin balas, status jadi 'proses'
        if ($tiket->status == 'terbuka' || $tiket->status == 'selesai') {
            $tiket->update(['status' => 'proses']);
        }

        return back()->with('berhasil', 'Balasan berhasil dikirim.');
    }

    public function gantiStatus(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $tiket->update(['status' => $request->status]);

        return back()->with('berhasil', 'Status tiket diperbarui menjadi ' . $request->status);
    }

    public function hapus($id)
    {
        $tiket = Tiket::findOrFail($id);
        $tiket->delete();

        return redirect('/admin/tiket')->with('berhasil', 'Tiket berhasil dihapus.');
    }
}
