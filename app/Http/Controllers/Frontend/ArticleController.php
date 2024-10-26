<?php

namespace App\Http\Controllers\FrontEnd;

use App\Models\Article;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function show(string $slug)
    {
        // eloquent
        $article = Article::with('category:id,name,slug', 'user:id,name', 'tags:id,name,slug')
            ->where('slug', $slug)
            ->first();
        if (!$article) {
            return view('frontend.custom-error.404', [
                'url' => url('/article/' . $slug),
            ]);
        }
        
        return view('frontend.article.show', [
            'article' => $article
        ]);
    }
}
