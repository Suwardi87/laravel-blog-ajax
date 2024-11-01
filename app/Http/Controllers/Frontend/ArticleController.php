<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Services\Frontend\TagService;
use App\Http\Controllers\Services\Frontend\ArticleService;
use App\Http\Controllers\Services\Frontend\CategoryService;
use App\Mail\StatusMail;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleService $articleService,
        private CategoryService $categoryService,
        private TagService $tagService
    ) {}

    public function index()
    {
        return view('frontend.article.index', [
            'articles' => $this->articleService->all(),
        ]);
    }

    public function show(string $slug)
    {
        // eloquent
        $article = $this->articleService->getFirstBy('slug', $slug, true);

        if ($article == null) {
            return view('frontend.custom-error.404', [
                'url' => url('/article/' . $slug),
            ]);
        }

        // add view
        $article->increment('views');

        // get category
        $categories = $this->categoryService->randomCategory();

        return view('frontend.article.show', [
            'article' => $article,
            'related_articles' => $this->articleService->relatedArticles($article->slug),
        ]);
    }


}
