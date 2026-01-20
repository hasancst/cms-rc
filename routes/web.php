<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicController;

// Authentication Routes
Route::get('/mlebu', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/mlebu', [LoginController::class, 'login']);
Route::post('/keluar', [LoginController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', [PublicController::class, 'index']);
Route::get('/berita', [PublicController::class, 'berita']);
Route::get('/berita/kategori/{slug}', [PublicController::class, 'kategoriBerita']);
Route::get('/kategori-berita', function() { return redirect('/berita'); });
Route::get('/kategori-berita/{slug}', [PublicController::class, 'kategoriBerita']);
Route::get('/berita/kategori-berita/{slug}', [PublicController::class, 'kategoriBerita']);
Route::get('/berita/{slug}', [PublicController::class, 'detailBerita']);
Route::get('/artikel', [PublicController::class, 'artikel']);
Route::get('/artikel/kategori/{slug}', [PublicController::class, 'kategoriArtikel']);
Route::get('/artikel/{slug}', [PublicController::class, 'detailArtikel']);
Route::get('/video', [PublicController::class, 'video']);
Route::get('/video/{slug}', [PublicController::class, 'detailVideo']);
Route::get('/kontak', [PublicController::class, 'kontak']);
Route::post('/kontak', [PublicController::class, 'kirimKontak']);
Route::get('/tentang', [PublicController::class, 'tentang']);
Route::get('/redaksi', [PublicController::class, 'redaksi']);
Route::get('/kebijakan', [PublicController::class, 'kebijakan']);
Route::get('/syarat', [PublicController::class, 'syarat']);

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    
    // Modul
    Route::middleware('cek_izin:kelola-modul')->group(function () {
        Route::get('/modul', [AdminController::class, 'indeksModul']);
        Route::post('/modul/pasang', [AdminController::class, 'pasangModul']);
        Route::post('/modul/aktifkan', [AdminController::class, 'aktifkanModul']);
        Route::post('/modul/nonaktifkan', [AdminController::class, 'nonaktifkanModul']);
        Route::post('/modul/copot', [AdminController::class, 'copotModul']);
        Route::post('/modul/unggah', [AdminController::class, 'unggahModul']);
    });

    // Tema
    Route::middleware('cek_izin:kelola-tema')->group(function () {
        Route::get('/tema', [AdminController::class, 'indeksTema']);
        Route::post('/tema/aktifkan', [AdminController::class, 'aktifkanTema']);
        Route::post('/tema/perbarui', [AdminController::class, 'perbaruiTema']);
    });

    // Pengaturan
    Route::middleware('cek_izin:kelola-pengaturan')->group(function () {
        Route::get('/pengaturan', [AdminController::class, 'indeksPengaturan']);
        Route::post('/pengaturan', [AdminController::class, 'simpanPengaturan']);
        Route::post('/media/unggah', [AdminController::class, 'unggahMedia'])->name('admin.media.unggah');
    });
});
