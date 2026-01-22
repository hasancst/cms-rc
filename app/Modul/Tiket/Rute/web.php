<?php

use Illuminate\Support\Facades\Route;
use App\Modul\Tiket\Http\Controllers\TiketController;

Route::prefix('admin/tiket')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [TiketController::class, 'indeks']);
    Route::get('/tambah', [TiketController::class, 'tambah']);
    Route::post('/tambah', [TiketController::class, 'simpan']);
    Route::get('/detail/{id}', [TiketController::class, 'detail']);
    Route::post('/balas/{id}', [TiketController::class, 'balas']);
    Route::post('/status/{id}', [TiketController::class, 'gantiStatus']);
    Route::post('/hapus/{id}', [TiketController::class, 'hapus']);
});
