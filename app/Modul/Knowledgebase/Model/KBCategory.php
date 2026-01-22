<?php

namespace App\Modul\Knowledgebase\Model;

use Illuminate\Database\Eloquent\Model;

class KBCategory extends Model
{
    protected $table = 'kb_categories';
    protected $guarded = [];

    public function articles()
    {
        return $this->hasMany(KBArticle::class, 'category_id')->where('aktif', true)->orderBy('urutan');
    }
}
