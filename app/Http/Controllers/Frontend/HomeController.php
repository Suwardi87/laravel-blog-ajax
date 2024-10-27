<?php

namespace App\Http\Controllers\FrontEnd;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Frontend\TagService;
use App\Http\Controllers\Services\Frontend\ArticleService;
use App\Http\Controllers\Services\Frontend\CategoryService;

class HomeController extends Controller
{
    public function __construct(
        private ArticleService $articleService,
        private CategoryService $categoryService,
        private TagService $tagService
    ) {}
    public function index()
    {
        // artikel terbaru
        $main_post = Article::with('category:id,name', 'user:id,name')
            ->select('id', 'user_id', 'category_id', 'title', 'slug', 'content', 'published', 'is_confirm', 'views', 'image')
            ->latest()
            ->where('published', true)
            ->where('is_confirm', true)
            ->first();
        // artikel terpopuler
        $top_view = Article::with('category:id,name', 'tags:id,name')
            ->select('id', 'category_id', 'title', 'slug', 'content', 'published', 'is_confirm', 'views', 'image')
            ->orderBy('views', 'asc')
            ->where('published', true)
            ->where('is_confirm', true)
            ->first();
        // artikel terbaru semua kategori
        $main_post_all = Article::with('category:id,name')
            ->select('id', 'category_id', 'title', 'slug', 'published', 'is_confirm', 'views', 'image')
            ->latest()
            ->where('published', true)
            ->where('is_confirm', true)
            ->where('id', '!=', $main_post->id)
            ->limit(6) // membatasi hanya 6
            ->get();
        return view('frontend.home.index', [
            'main_post' => $main_post,
            'top_view' => $top_view,
            'main_post_all' => $main_post_all,
            'articles' => $this->articleService->all(),
            'categories' => $this->categoryService->all(),
            'popular_articles' => $this->articleService->popularArticles(),
            'tags' => $this->tagService->all()
        ]);
    }
}
