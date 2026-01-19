<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = \DB::table('pengguna')->first();
        $authorId = $author ? $author->id : 1;

        $categories = [
            ['nama' => 'Teknologi', 'slug' => 'teknologi'],
            ['nama' => 'Hukum', 'slug' => 'hukum'],
            ['nama' => 'Cyber Security', 'slug' => 'cyber-security'],
            ['nama' => 'Edukasi', 'slug' => 'edukasi'],
        ];

        foreach ($categories as $cat) {
            \DB::table('kategori_berita')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $news = [
            [
                'judul' => 'Tren Keamanan Siber 2026: Ancaman AI Generatif',
                'slug' => 'tren-keamanan-siber-2026-ancaman-ai-generatif',
                'isi' => '<p>Perkembangan AI generatif membawa tantangan baru dalam dunia keamanan siber. Serangan phishing kini menjadi lebih canggih dan sulit dideteksi...</p>',
                'status' => 'publikasi',
                'unggulan' => true,
                'kategori_id' => 3, // Cyber Security
                'penulis_id' => $authorId,
            ],
            [
                'judul' => 'Memahami Hak Kekayaan Intelektual bagi Kreator Digital',
                'slug' => 'memahami-hak-kekayaan-intelektual-bagi-kreator-digital',
                'isi' => '<p>Di era digital, perlindungan karya menjadi sangat krusial. Kreator perlu memahami prosedur pendaftaran HAKI untuk melindungi aset mereka...</p>',
                'status' => 'publikasi',
                'unggulan' => false,
                'kategori_id' => 2, // Hukum
                'penulis_id' => $authorId,
            ],
        ];

        foreach ($news as $item) {
            \DB::table('berita')->updateOrInsert(
                ['slug' => $item['slug']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
