<?php

namespace App\Modul\Knowledgebase\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modul\Knowledgebase\Model\KBCategory;
use App\Modul\Knowledgebase\Model\KBArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KBPublicController extends Controller
{
    public function index(Request $request)
    {
        $categories = KBCategory::withCount('articles')->orderBy('urutan')->get();
        $popularArticles = KBArticle::where('aktif', true)->orderBy('views', 'desc')->limit(5)->get();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();

        // Search logic
        $search = $request->q;
        $results = null;
        if($search) {
            $results = KBArticle::where('aktif', true)
                ->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%$search%")
                      ->orWhere('konten', 'like', "%$search%");
                })->get();
        }

        return view('knowledgebase::public.index', compact('categories', 'popularArticles', 'pengaturan', 'results', 'search'));
    }

    public function showCategory($slug)
    {
        $category = KBCategory::where('slug', $slug)->firstOrFail();
        $articles = KBArticle::where('category_id', $category->id)->where('aktif', true)->orderBy('urutan')->get();
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        
        return view('knowledgebase::public.category', compact('category', 'articles', 'pengaturan'));
    }

    public function showArticle($slug)
    {
        $article = KBArticle::with('category')->where('slug', $slug)->firstOrFail();
        
        // Update views
        $article->increment('views');
        
        $relatedArticles = KBArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('aktif', true)
            ->limit(5)
            ->get();
            
        $pengaturan = DB::table('pengaturan')->pluck('nilai', 'kunci')->toArray();
        
        return view('knowledgebase::public.article', compact('article', 'relatedArticles', 'pengaturan'));
    }
}
