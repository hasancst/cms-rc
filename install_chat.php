<?php

/**
 * Chat Module Installation Script
 * Run this file once to register the Chat module
 * HANYA untuk dijalankan via CLI: php install_chat.php
 */

// Cegah akses via web server
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Forbidden: Script ini hanya bisa dijalankan via command line.');
}

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check if module already exists
    $exists = DB::table('modul')->where('slug', 'Chat')->exists();
    
    if ($exists) {
        echo "✓ Chat module sudah terdaftar.\n";
    } else {
        // Register module
        DB::table('modul')->insert([
            'nama' => 'Chat Widget',
            'slug' => 'Chat',
            'versi' => '1.0.0',
            'deskripsi' => 'AI-powered chat widget dengan integrasi Knowledge Base dan auto-escalation ke Ticketing System',
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✓ Chat module berhasil didaftarkan.\n";
    }
    
    // Run migrations
    echo "\nMenjalankan migrasi...\n";
    Artisan::call('migrate', ['--path' => 'app/Modul/Chat/Database/Migrasi']);
    echo Artisan::output();
    
    echo "\n✅ Instalasi Chat module selesai!\n";
    echo "\nLangkah selanjutnya:\n";
    echo "1. Buka /admin/chat untuk membuat widget\n";
    echo "2. Salin embed code dan pasang di website Anda\n";
    echo "3. Chat widget siap digunakan!\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
