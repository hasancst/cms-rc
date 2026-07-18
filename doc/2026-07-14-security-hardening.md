# Security Hardening Plan — imakecustom.com

> **Tanggal Review:** 14 Juli 2026
> **Reviewer:** Kiro Security Review (via `security-reviewer` skill)
> **Tech Stack:** Laravel (PHP) + MySQL + S3
> **Status:** ✅ Selesai — 17 fix dieksekusi (13 original + 4 dari CSRF recheck)
> **Prioritas:** URGENT — terdapat 6 temuan Critical yang aktif di production

---

## Ringkasan Temuan

| Severity | Jumlah | Keterangan |
|----------|--------|------------|
| 🔴 Critical | 6 | phpinfo terbuka, path traversal, command injection, SQL injection, missing auth |
| 🟠 High | 4 | Path traversal ZIP, session cookie, error leakage, weak file validation |
| 🟡 Medium | 3 | CORS dev origin, backup di root, debug routes |

---

## Checklist Perbaikan

### 🔴 FIX-1 — Hapus `test_php.php` dari folder public

**File:** `public/test_php.php`
**Severity:** 🔴 Critical
**Effort:** 1 menit

**Masalah:**
File `phpinfo()` aktif dan bisa diakses siapapun di `https://imakecustom.com/test_php.php`.
Mengekspos versi PHP, semua environment variables (termasuk `DB_PASSWORD`, `APP_KEY`), path absolut filesystem, modul baiPHP aktif, dan konfigurasi server penuh.

```
# Verifikasi sebelum hapus:
curl -s https://imakecustom.com/test_php.php | head -5

# Fix:
rm public/test_php.php
```

- [ ] Hapus file `public/test_php.php`
- [ ] Verifikasi sudah tidak bisa diakses: `curl -I https://imakecustom.com/test_php.php` → harus 404

---

### 🔴 FIX-2 — Path traversal di `unduhBackup` dan `hapusBackup`

**File:** `app/Http/Controllers/Admin/AdminController.php`
**Severity:** 🔴 Critical
**Effort:** 15 menit

**Masalah:**
Parameter `$file` dari URL langsung digabung ke path tanpa sanitasi — penyerang admin bisa download atau hapus file apapun di server:

```
# Contoh exploit:
GET /admin/backup/unduh/../../../../.env
GET /admin/backup/unduh/../../../etc/passwd
DELETE /admin/backup/hapus/../../../../config/database.php
```

**Fix:**
```php
public function unduhBackup($file)
{
    // Hanya izinkan nama file backup yang valid — tidak ada path traversal
    $file = basename($file);
    if (!preg_match('/^backup-[\d-]+\.(sql|sql\.gz)$/', $file)) {
        abort(400, 'Nama file tidak valid');
    }
    $path = storage_path('app/backups/' . $file);
    abort_if(!File::exists($path), 404, 'File tidak ditemukan');
    return response()->download($path);
}

public function hapusBackup($file)
{
    $file = basename($file);
    if (!preg_match('/^backup-[\d-]+\.(sql|sql\.gz)$/', $file)) {
        abort(400, 'Nama file tidak valid');
    }
    $path = storage_path('app/backups/' . $file);
    abort_if(!File::exists($path), 404);
    File::delete($path);
    return back()->with('success', 'Backup berhasil dihapus');
}
```

- [ ] Tambahkan `basename()` + regex validation di `unduhBackup()`
- [ ] Tambahkan `basename()` + regex validation di `hapusBackup()`
- [ ] Test: akses `/admin/backup/unduh/../../../../.env` harus 400

---

### 🔴 FIX-3 — Path traversal di `restoreBackup` via fallback `base_path()`

**File:** `app/Http/Controllers/Admin/AdminController.php`
**Severity:** 🔴 Critical
**Effort:** 10 menit

**Masalah:**
Jika file tidak ditemukan di `storage/app/backups/`, ada fallback yang mengizinkan path ke file apapun di project:

```php
// BERBAHAYA — jangan lakukan ini
if (File::exists(base_path($file))) {
    $path = base_path($file); // bisa arahkan ke file apapun!
}
// lalu di-restore ke database — bisa merusak data
```

**Fix:**
```php
public function restoreBackup(Request $request)
{
    $file = basename($request->file);
    if (!preg_match('/^backup-[\d-]+\.(sql|sql\.gz)$/', $file)) {
        return response()->json(['pesan' => 'Nama file tidak valid'], 400);
    }

    $path = storage_path('app/backups/' . $file);

    if (!File::exists($path)) {
        return response()->json(['pesan' => 'File backup tidak ditemukan'], 404);
    }

    // Hapus fallback base_path() — tidak ada pencarian di luar folder backups
    $result = $this->dbBackup->restore($path);
    // ...
}
```

- [ ] Hapus blok fallback `base_path($file)`
- [ ] Tambahkan validasi `basename()` + regex di `restoreBackup()`
- [ ] Batasi error response — jangan kembalikan output psql mentah ke client

---

### 🔴 FIX-4 — Command injection di `DatabaseBackup`

**File:** `app/Inti/DatabaseBackup.php`
**Severity:** 🔴 Critical
**Effort:** 10 menit

**Masalah:**
Credentials database dimasukkan langsung ke string shell command tanpa `escapeshellarg()`:

```php
// BERBAHAYA — command injection jika password mengandung ' ; $ () etc
$command = "PGPASSWORD='{$dbPass}' {$pgDumpPath} -h {$dbHost} -p {$dbPort} -U {$dbUser} {$dbName} > {$path}";
exec($command, $output, $returnCode);
```

**Fix:**
```php
public function backup(string $path): array
{
    $dbPass  = env('DB_PASSWORD', '');
    $dbHost  = env('DB_HOST', '127.0.0.1');
    $dbPort  = env('DB_PORT', '5432');
    $dbUser  = env('DB_USERNAME', '');
    $dbName  = env('DB_DATABASE', '');

    // Semua argumen di-escape untuk mencegah command injection
    $command = sprintf(
        'PGPASSWORD=%s %s -h %s -p %s -U %s %s > %s 2>&1',
        escapeshellarg($dbPass),
        escapeshellarg($this->pgDumpPath),
        escapeshellarg($dbHost),
        escapeshellarg($dbPort),
        escapeshellarg($dbUser),
        escapeshellarg($dbName),
        escapeshellarg($path)
    );

    exec($command, $output, $returnCode);
    return ['code' => $returnCode, 'output' => implode("\n", $output)];
}

public function restore(string $path): array
{
    $dbPass  = env('DB_PASSWORD', '');
    $dbHost  = env('DB_HOST', '127.0.0.1');
    $dbPort  = env('DB_PORT', '5432');
    $dbUser  = env('DB_USERNAME', '');
    $dbName  = env('DB_DATABASE', '');

    $command = sprintf(
        'PGPASSWORD=%s %s -h %s -p %s -U %s -d %s -f %s 2>&1',
        escapeshellarg($dbPass),
        escapeshellarg($this->psqlPath),
        escapeshellarg($dbHost),
        escapeshellarg($dbPort),
        escapeshellarg($dbUser),
        escapeshellarg($dbName),
        escapeshellarg($path)
    );

    exec($command, $output, $returnCode);
    return ['code' => $returnCode, 'output' => implode("\n", $output)];
}
```

- [ ] Ganti semua string interpolasi di `backup()` dengan `sprintf()` + `escapeshellarg()`
- [ ] Ganti semua string interpolasi di `restore()` dengan `sprintf()` + `escapeshellarg()`
- [ ] Verifikasi tidak ada command lain yang masih pakai string interpolasi langsung

---

### 🔴 FIX-5 — SQL injection via `orderByRaw` tanpa binding

**File:** `app/Http/Controllers/PublicController.php`
**Severity:** 🔴 Critical
**Effort:** 10 menit

**Masalah:**
`$slug` dari tabel `stat_pengunjung` (yang bisa dikontrol via URL tracking) dimasukkan ke `orderByRaw` hanya dengan `str_replace("'", "''")` yang tidak cukup untuk semua SQL injection patterns:

```php
// BERBAHAYA
$orderBy = "CASE ";
foreach ($slugViews as $slug => $view) {
    $slugSanitized = str_replace("'", "''", $slug); // tidak cukup aman!
    $orderBy .= "WHEN slug = '$slugSanitized' THEN $view ";
}
$popQuery->orderByRaw($orderBy);
```

**Fix — gunakan parameter binding:**
```php
$cases    = [];
$bindings = [];

foreach ($slugViews as $slug => $view) {
    $cases[]    = "WHEN slug = ? THEN ?";
    $bindings[] = (string) $slug;
    $bindings[] = (int) $view;
}

if (!empty($cases)) {
    $orderByRaw = "CASE " . implode(" ", $cases) . " ELSE 0 END DESC";
    $popQuery->orderByRaw($orderByRaw, $bindings);
}
```

- [ ] Ganti `str_replace` sanitasi dengan parameter binding `?` di `orderByRaw()`
- [ ] Pastikan `$view` di-cast ke `int` untuk mencegah injection via nilai view
- [ ] Test dengan slug yang mengandung karakter SQL: `slug = '1' OR '1'='1`

---

### 🔴 FIX-6 — Client routes tanpa `cek_izin` middleware (privilege escalation)

**File:** `routes/web.php`
**Severity:** 🔴 Critical
**Effort:** 5 menit

**Masalah:**
Route CRUD client dan `extend-trial` hanya dilindungi `middleware('auth')` — semua user yang sudah login (bukan hanya admin dengan izin) bisa akses dan memodifikasi data client, bahkan meng-extend trial secara gratis:

```php
// BERBAHAYA — hanya 'auth', tidak ada cek izin spesifik
Route::prefix('client')->group(function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/{id}/extend-trial', [ClientController::class, 'extendTrial']);
    Route::post('/hapus/{id}', [ClientController::class, 'destroy']);
    // ...
});
```

**Fix:**
```php
// Sesuaikan nama izin dengan yang sudah ada di sistem cek_izin
Route::prefix('client')->middleware('cek_izin:kelola-client')->group(function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/{id}/extend-trial', [ClientController::class, 'extendTrial']);
    Route::post('/hapus/{id}', [ClientController::class, 'destroy']);
    // ...
});
```

- [ ] Cek nama izin yang tersedia di tabel permissions / middleware `cek_izin`
- [ ] Tambahkan `middleware('cek_izin:kelola-client')` ke group route client
- [ ] Test: login sebagai user tanpa izin → harus ditolak (403)
- [ ] Test: login sebagai admin dengan izin → harus bisa akses

---

### 🟠 FIX-7 — Path traversal via nama file ZIP saat unggah modul

**File:** `app/Http/Controllers/Admin/AdminController.php`
**Severity:** 🟠 High
**Effort:** 5 menit

**Masalah:**
Nama file ZIP dipakai langsung sebagai nama folder modul tanpa sanitasi:

```php
$slug = pathinfo($request->file('file_zip')->getClientOriginalName(), PATHINFO_FILENAME);
$tujuan = app_path("Modul/{$slug}"); // slug bisa mengandung ../
$extractor->ekstrak($zipPath, $tujuan);
```

**Fix:**
```php
$rawName = pathinfo($request->file('file_zip')->getClientOriginalName(), PATHINFO_FILENAME);
$slug    = preg_replace('/[^a-zA-Z0-9_-]/', '', $rawName); // hanya alfanumerik

if (empty($slug)) {
    return back()->withErrors(['file_zip' => 'Nama file ZIP tidak valid']);
}

$tujuan = app_path("Modul/{$slug}");
$extractor->ekstrak($zipPath, $tujuan);
```

- [ ] Tambahkan `preg_replace` sanitasi pada slug nama folder
- [ ] Tambahkan validasi magic bytes ZIP (check header `PK\x03\x04`) sebelum ekstrak
- [ ] Test: upload ZIP dengan nama `../../routes.zip` → harus ditolak

---

### 🟠 FIX-8 — `SESSION_SECURE_COOKIE` tidak di-set

**File:** `.env`
**Severity:** 🟠 High
**Effort:** 1 menit

**Masalah:**
`config/session.php` membaca `env('SESSION_SECURE_COOKIE')` — jika tidak ada di `.env`, nilainya `null` (false), sehingga session cookie bisa dikirim via HTTP bukan hanya HTTPS.

**Fix — tambahkan ke `.env`:**
```
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

- [ ] Tambahkan `SESSION_SECURE_COOKIE=true` ke `.env`
- [ ] Tambahkan `SESSION_SAME_SITE=lax` ke `.env` (proteksi CSRF tambahan)
- [ ] Verifikasi: cek cookie `Set-Cookie` header — harus ada flag `Secure; SameSite=Lax`

---

### 🟠 FIX-9 — Error detail database/shell bocor ke client

**File:** `app/Http/Controllers/Admin/AdminController.php`
**Severity:** 🟠 High
**Effort:** 10 menit

**Masalah:**
Output psql mentah dikembalikan ke response JSON:

```php
return response()->json([
    'pesan' => 'Gagal restore. ' . ($result['error'] ?? '') . "\n" . ($result['output'] ?? '')
], 500);
```

Output ini bisa mengandung path server, versi database, atau informasi internal.

**Fix:**
```php
// Log detail error ke server log — jangan kirim ke client
\Log::error('[restoreBackup] Failed', ['output' => $result['output'] ?? '']);

return response()->json([
    'pesan' => 'Restore gagal. Silakan cek log server.'
], 500);
```

- [ ] Ganti semua response yang mengembalikan `$result['output']` / `$result['error']` dengan pesan generik
- [ ] Pastikan error detail di-log ke `storage/logs/laravel.log` untuk debugging

---

### 🟠 FIX-10 — Validasi file ZIP hanya cek ekstensi, tidak magic bytes

**File:** `app/Http/Controllers/Admin/AdminController.php`
**Severity:** 🟠 High
**Effort:** 10 menit

**Masalah:**
File upload modul hanya memvalidasi ekstensi `.zip` — file PHP yang di-rename `.zip` bisa lolos dan di-ekstrak ke folder `Modul/`:

```php
if (strtolower($request->file('file_zip')->getClientOriginalExtension()) !== 'zip') {
    return back()->withErrors(['file_zip' => 'Harus file ZIP']);
}
```

**Fix — validasi magic bytes:**
```php
// Validasi MIME type menggunakan finfo (magic bytes), bukan ekstensi
$file    = $request->file('file_zip');
$finfo   = new \finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file->getRealPath());

if ($mimeType !== 'application/zip' && $mimeType !== 'application/x-zip-compressed') {
    return back()->withErrors(['file_zip' => 'File bukan ZIP yang valid']);
}
```

- [ ] Tambahkan validasi MIME type via `finfo` (magic bytes)
- [ ] Tambahkan validasi ukuran maksimal file ZIP
- [ ] Setelah ekstrak — scan isi folder untuk memastikan tidak ada file `.php` di dalamnya

---

### 🟡 FIX-11 — CORS mengizinkan origin development di production

**File:** `config/cors.php`
**Severity:** 🟡 Medium
**Effort:** 2 menit

**Masalah:**
Origin `http://custom.local` (development) masih ada di konfigurasi production CORS:

```php
'allowed_origins' => [
    'http://custom.local', // dev origin di production!
    'https://imakecustom.com',
    // ...
],
```

**Fix:**
```php
'allowed_origins' => [
    'https://imakecustom.com',
    'https://www.imakecustom.com',
    // tambahkan origin production lain yang diperlukan
],
```

- [ ] Hapus `http://custom.local` dari `allowed_origins`
- [ ] Pastikan semua origin yang tersisa adalah HTTPS production origins

---

### 🟡 FIX-12 — File backup SQL di root project

**Severity:** 🟡 Medium
**Effort:** 5 menit

**Masalah:**
File `backup-*.sql` ditemukan di root project. Jika web server salah konfigurasi, file ini bisa diakses dan mengekspos seluruh isi database.

**Fix:**
```bash
# Pindahkan ke storage yang tidak bisa diakses web
mv /www/wwwroot/imakecustom.com/backup-*.sql /www/wwwroot/imakecustom.com/storage/app/backups/

# Verifikasi tidak ada backup di root
ls /www/wwwroot/imakecustom.com/*.sql
```

- [ ] Pindahkan semua file `*.sql` dari root ke `storage/app/backups/`
- [ ] Cek `.gitignore` — pastikan `*.sql` dan `storage/app/backups/` sudah di-exclude
- [ ] Verifikasi file tidak bisa diakses via web: `curl -I https://imakecustom.com/backup-*.sql` → harus 404

---

### 🟡 FIX-13 — File `clean_modules.php` dan `install_chat.php` di root project

**Severity:** 🟡 Medium
**Effort:** 5 menit

**Masalah:**
Ada file PHP di root project yang tidak jelas apakah bisa diakses atau mengandung fungsi berbahaya:
- `clean_modules.php`
- `install_chat.php`

**Fix:**
- [ ] Baca dan review isi kedua file ini
- [ ] Jika hanya script CLI — pastikan tidak bisa diakses via web (tambahkan check `php_sapi_name() === 'cli'`)
- [ ] Jika tidak diperlukan lagi — hapus
- [ ] Verifikasi akses web: `curl -I https://imakecustom.com/clean_modules.php` → harus 404 atau 403

---

## Tracking Progres

| Fix | Deskripsi | Severity | Status |
|-----|-----------|----------|--------|
| FIX-1 | Hapus `test_php.php` | 🔴 Critical | ✅ Selesai |
| FIX-2 | Path traversal `unduhBackup` + `hapusBackup` | 🔴 Critical | ✅ Selesai |
| FIX-3 | Path traversal `restoreBackup` | 🔴 Critical | ✅ Selesai |
| FIX-4 | Command injection `DatabaseBackup` | 🔴 Critical | ✅ Selesai |
| FIX-5 | SQL injection `orderByRaw` | 🔴 Critical | ✅ Selesai |
| FIX-6 | Missing `cek_izin` di client routes | 🔴 Critical | ✅ Selesai |
| FIX-7 | Path traversal nama file ZIP | 🟠 High | ✅ Selesai |
| FIX-8 | `SESSION_SECURE_COOKIE` tidak di-set | 🟠 High | ✅ Selesai |
| FIX-9 | Error detail bocor ke client | 🟠 High | ✅ Selesai |
| FIX-10 | Validasi ZIP hanya cek ekstensi | 🟠 High | ✅ Selesai |
| FIX-11 | CORS origin development di production | 🟡 Medium | ✅ Selesai |
| FIX-12 | File backup SQL di root project | 🟡 Medium | ✅ Selesai (tidak ada file) |
| FIX-13 | `clean_modules.php` + `install_chat.php` di root | 🟡 Medium | ✅ Selesai |
| FIX-14 | `admin/video` tanpa `web` middleware → CSRF bypass | 🔴 Critical | ✅ Selesai |
| FIX-15 | `admin/layanan` tanpa `web` + `auth` middleware | 🔴 Critical | ✅ Selesai |
| FIX-16 | `api/chat` CSRF exempt tidak eksplisit | 🟡 Medium | ✅ Selesai |
| FIX-17 | Hapus GET route `/admin/video/hapus/{id}` → ganti ke DELETE+CSRF | 🟠 High | ✅ Selesai |

---

## Yang Sudah Baik ✅

- `APP_DEBUG=false` di production
- CSRF protection aktif (Laravel default middleware)
- File upload tiket menggunakan `mimes:` validation + max size
- API routes menggunakan middleware autentikasi `shopify_bridge`
- CORS tidak menggunakan wildcard `*`
- Semua admin routes di balik `middleware('auth')`
- Session cookie menggunakan `http_only: true`
- `APP_ENV=production` sudah benar

---

## Referensi

- [OWASP Path Traversal](https://owasp.org/www-community/attacks/Path_Traversal)
- [OWASP Command Injection](https://owasp.org/www-community/attacks/Command_Injection)
- [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- File utama yang relevan:
  - `public/test_php.php` — harus dihapus
  - `app/Inti/DatabaseBackup.php` — command injection
  - `app/Http/Controllers/Admin/AdminController.php` — path traversal, error leakage
  - `app/Http/Controllers/PublicController.php` — SQL injection
  - `routes/web.php` — missing authorization
  - `config/cors.php` — dev origin di production
  - `.env` — session secure cookie
