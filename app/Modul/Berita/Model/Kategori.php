<?php

namespace App\Modul\Berita\Model;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori_berita';
    protected $fillable = ['nama', 'slug'];

    public function berita()
    {
        return $this->belongsToMany(Berita::class, 'berita_memiliki_kategori', 'kategori_id', 'berita_id');
    }
}
