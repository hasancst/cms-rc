<?php

use Illuminate\Support\Facades\Route;
use App\Modul\Berita\Http\Controller\BeritaController;
use App\Modul\Berita\Http\Controller\KategoriController;

Route::middleware(['web', 'auth'])->prefix('admin/berita')->group(function () {
    Route::get('/', [BeritaController::class, 'indeks']);
    Route::get('/tambah', [BeritaController::class, 'tambah']);
    Route::post('/tambah', [BeritaController::class, 'simpan']);
    Route::get('/ubah/{id}', [BeritaController::class, 'ubah']);
    Route::post('/ubah/{id}', [BeritaController::class, 'perbarui']);
    Route::post('/unggulan/{id}', [BeritaController::class, 'toggleUnggulan']);
    Route::post('/quick-kategori/{id}', [BeritaController::class, 'quickKategori']);
    Route::delete('/hapus/{id}', [BeritaController::class, 'hapus']);
    
    // Kategori
    Route::get('/kategori', [KategoriController::class, 'indeks']);
    Route::post('/kategori', [KategoriController::class, 'simpan']);
    Route::get('/kategori/ubah/{id}', [KategoriController::class, 'ubah']);
    Route::post('/kategori/ubah/{id}', [KategoriController::class, 'perbarui']);
    Route::delete('/kategori/hapus/{id}', [KategoriController::class, 'hapus']);
});
