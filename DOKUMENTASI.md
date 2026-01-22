# Dokumentasi CMS Laravel Modular Anti-Gravity

## Struktur Folder
- `app/Inti`: Core sistem (Module Loader, Theme Manager, dll).
- `app/Modul`: Tempat penyimpanan modul (plugin).
- `resources/theme`: Tempat penyimpanan tema UI.
- `database/migrations`: Migrasi core (Bahasa Indonesia).

## Cara Membuat Modul Baru
1. Buat folder di `app/Modul/{NamaModul}`.
2. Buat file `manifest.json` yang berisi informasi modul.
3. Buat ServiceProvider `app/Modul/{NamaModul}/{NamaModul}ServiceProvider.php`.
4. Jalankan `php artisan modul:pasang {slug}` untuk menginstal.

## Lifecycle Modul
- **Pasang**: Menjalankan migrasi di folder `Database/Migrasi` milik modul dan mendaftarkannya ke DB.
- **Aktifkan**: Mengizinkan Laravel me-load ServiceProvider modul tersebut.
- **Nonaktifkan**: Menghentikan load ServiceProvider tanpa menghapus data.
- **Copot**: Me-rollback migrasi dan menghapus catatan modul dari DB.

## Cara Membuat Tema Baru
1. Buat folder di `resources/theme/{nama-tema}`.
2. Buat file `theme.json`.
3. Gunakan `@extends('tema::layout.main')` (atau sejenisnya) untuk menggunakan view tema.

## Standar Kode
- Wajib menggunakan Bahasa Indonesia untuk nama tabel, kolom, function, dan komentar.
- Gunakan Event (Laravel Events) untuk komunikasi antar modul.
- Autoloading mengikuti PSR-4 dengan namespace `App\Modul\{Slug}`.
