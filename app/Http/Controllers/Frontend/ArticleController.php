<?php

namespace App\Http\Controllers\FrontEnd;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Frontend\ArticleService;
use App\Http\Controllers\Services\Frontend\CategoryService;
use App\Http\Controllers\Services\Frontend\TagService;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleService $articleService,
        private CategoryService $categoryService,
        private TagService $tagService
    ) {}

    public function index()
    {
        $articles = $this->articleService->all();

        $categories = $this->categoryService->randomCategory();

        return view('frontend.article.index', [
            'articles' => $articles,
            'categories' => $categories,
            'popular_articles' => $this->articleService->popularArticles(),
            'tags' => $this->tagService->all(),
            // 'related_articles' => $this->articleService->relatedArticles($article->slug),
        ]);
    }
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

        // // add view
        $article->increment('views');

         // get category
         $categories = $this->categoryService->randomCategory();

         return view('frontend.article.show', [
             'article' => $article,
             'categories' => $categories,
             'popular_articles' => $this->articleService->popularArticles(),
             'tags' => $this->tagService->all(),
             'related_articles' => $this->articleService->relatedArticles($article->slug),
         ]);
    }
}
